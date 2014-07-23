<?php
App::import('Vendor', 'OAuth/OAuthClient', 'DryXML/DryXML.php');
class UsersController extends AppController {
    public $uses = array('User', 'Employee', 'Request');
    public $helpers = array('Request');
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('login', 'logout', 'uitid', 'callback', 'error', 'associate', 'requestHandled', 'emergencyLogin');
    }

    public function login() {
        $this->layout = 'login';
        if($this->Auth->loggedIn()){
            $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
        }
        else {if ($this->request->is('post')) {
            if ($this->Auth->login()) {
               //return $this->redirect(array('controller' => 'CalendarItems'));
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }
    }

    public function logout() {
        $this->Session->destroy();
        return $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->set('users', $this->User->find('all'));
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        //$this->set('user', $this->User->read(null, $id));
        //$this->set('users', $this->User->find('all', array('fields' => array('id', 'email', 'name', 'surname', 'group'), 'order' => 'User.id ASC', 'group' => 'User.id')));

    }

    public function add() {
            if ($this->request->is('post')) {
                $this->User->create();
                if ($this->User->save($this->request->data)) {
                    //$this->Session->setFlash(__('The user has been saved'));
                    //return $this->redirect(array('action' => 'index'));
                }
                $this->Session->setFlash(
                    __('The user could not be saved. Please, try again.')
                );
            }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->request->onlyAllow('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

    public function uitid() {
        $client = $this->createClient();
        $requestToken = $client->getRequestToken(Configure::read('UiTID.server') . '/requestToken', 'http://' . $_SERVER["HTTP_HOST"] . $this->base .'/users/callback');
        if (!empty($requestToken)) {
            $this->Session->write('uitid_request_token', $requestToken);
            $this->redirect(Configure::read('UiTID.server') . '/auth/authorize?oauth_token=' . $requestToken->key);
        } else {

        }
    }

    public function callback() {
        $requestToken = $this->Session->read('uitid_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken(Configure::read('UiTID.server') .'/accessToken', $requestToken);
        if ($accessToken) {
            $user = $this->User->find('first', array('conditions' => array('User.uitid' => $accessToken->userId)));
            if(!empty($user)){
                if($user["User"]["status"] == 'active'){
                    $this->Auth->login($user['User']['id']);
                    $employee = $this->Employee->find('first',
                        array('conditions' => array('Employee.id' => $user["User"]["employee_id"]),
                            'fields' => array('Employee.id', 'Employee.employee_department_id', 'Employee.Name', 'Employee.surname', 'Role.id', 'Role.name', 'Role.adminpanel', 'Role.allow', 'Role.verifyuser', 'Role.edituser', 'Role.removeuser', 'Role.editcalendaritem')
                             ));
                    $this->Session->write('Auth', $employee);
                    $this->Session->write('Auth.User', $user);
                    $this->redirect('/');
                } else {
                    $this->redirect(array('action' => 'error'));
                }
            } else {
                $result = $client->request($accessToken->key, $accessToken->secret, $accessToken->userId, array('method' => 'GET', 'uri' =>  Configure::read('UiTID.server')  .'/user/' . $accessToken->userId . '?private=true'));
                $resultToArray = simplexml_load_string($result["body"]);
                $resultToArray = json_decode(json_encode($this->xmlToArray($resultToArray)), 1);
                //$result = $client->processResult($resultToArray);
                $this->redirect(array("controller" => "Employees", "action" => "associate", 'uitid' => base64_encode($accessToken->userId), 'email' => base64_encode($resultToArray["person"]["foaf:mbox"])));
            }
        }
    }

    public function error(){
        $this->layout = 'login';

    }


    private function createClient() {
        var_dump(Configure::read('UiTID.private'));
        return new OAuthClient(Configure::read('UiTID.public'), Configure::read('UiTID.private'));
    }

    public function associate(){
        if($this->request->is('post')){
            $this->User->create();
            $incomingData = $this->request->data;
                $newAssociation["User"]["employee_id"] = $incomingData["Employee"]["employeeId"];
                $newAssociation["User"]["email"] =base64_decode($incomingData["Employee"]["userEmail"]);
                $newAssociation["User"]["uitid"] = base64_decode($incomingData["Employee"]["uitid"]);
                $newAssociation["User"]["status"] = "requested";
                $existingUser = $this->User->find('first', array('conditions' => array('email' => $newAssociation["User"]["email"])));
                if(!empty($existingUser)){
                    $this->Session->setFlash('Iemand heeft zich al met dit email adres aangemeld');
                    $this->redirect(array('action' => 'error','error' => 1));
                } else {
                    $this->User->save($newAssociation);
                }

            $this->Session->setFlash(__('The user has been saved'));
            return $this->redirect(array('controller' => 'users', 'action' => 'requestHandled'));
        }
    }

    public function requestHandled(){
        $this->layout = 'login';
    }

    public function check(){
        echo '<pre>';
        var_dump($this->Session->read('Auth.Employee'));
        echo '</pre>';
    }

    public function approve($id = null){
        if(isset($id)){
            $access = $this->Session->read('Auth.Role.verifyuser');
            if($access == 1){
                    $useraccount = $this->User->findById($id);
                    if(!empty($useraccount)){
                        if($useraccount["User"]["status"] !== 'active'){
                            if($useraccount["User"]["status"] !== 'denied'){
                                if(!empty($useraccount["Employee"])){
                                    $useraccount["User"]["status"] = 'active';
                                    $useraccount["Employee"]["linked"] = 1;

                                    //sets the User Role to standard, so people wont register account based on their influence
                                    $useraccount["Employee"]["role_id"] = 3;

                                    $this->User->save($useraccount);

                                    $this->Session->setFlash('Je hebt toegang verschaft tot het systeem aan ' . $useraccount["User"]["email"]);
                                    $this->redirect(array('controller' => 'Admin', 'action' => 'index'));
                                }
                            }
                    } else {
                        $this->Session->setFlash('Deze gebruiker is al goedgekeurd.');
                        $this->redirect(array('controller' => 'Admin', 'action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash('Je hebt geen rechten om mensen toe te laten in het systeem.');
                    $this->redirect('/');
                }
            }
        }
    }

    public function deny($id = null){
        if(isset($id)){
            $access = $this->Session->read('Auth.Role.verifyuser');
            if($access == 1){
                $useraccount = $this->User->findById($id);
                if(!empty($useraccount)){
                    if($useraccount["User"]["status"] !== 'active'){
                        if(!empty($useraccount["Employee"])){
                            $useraccount["User"]["status"] = 'denied';
                            $useraccount["Employee"]["linked"] = 1;

                            //sets the User Role to standard, so people wont register account based on their influence
                            $useraccount["Employee"]["role_id"] = 3;

                            $this->User->save($useraccount);

                            $this->Session->setFlash('Je hebt toegang verschaft tot het systeem aan ' . $useraccount["User"]["email"]);
                            $this->redirect(array('controller' => 'Admin', 'action' => 'index'));
                        }
                    } else {
                        $this->Session->setFlash('Deze gebruiker is al goedgekeurd.');
                        $this->redirect(array('controller' => 'Admin', 'action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash('Je hebt geen rechten om mensen toe te laten in het systeem.');
                    $this->redirect('/');
                }
            }
        }
    }

    public function unlink($id = null){
        if(isset($id)){
            var_dump($id);
            $useraccount = $this->User->findById($id);
            $employee = $this->Employee->findById($useraccount["Employee"]["id"]);
            if($this->Session->read('Auth.Employee.id') == $employee["Employee"]["id"] or $this->Session->read('Auth.Role.edituser') == true){
                $this->User->delete($useraccount["User"]["id"]);
                if($this->Session->read('Auth.Role.edituser') !== true){
                    $this->Session->destroy();
                }
                $this->Session->setFlash('De werknemersgegevens werden losgekoppelt van de aanmeldgegevens');
                $this->redirect(array('action' => 'login'));
            } else {
                $this->redirect('/');
            }
        }
    }

    public function emergencyLogin(){
            /*if($this->request->is('post')){
                $user = $this->request->data;
                $employee = $this->Employee->findById();
                if($employee["Employee"]["password"] == decodePasswor()){
                    //Log user in
                }
            }*/
        }

    public function management(){
        $this->set('employee', $this->Employee->findById($this->Session->read('Auth.Employee.id')));
        $currentYear = date("Y");
        $this->set('linkedUsers', $this->User->find('all', array('conditions' => array(
            'User.employee_id' => $this->Session->read('Auth.Employee.id')
        ))));
        $this->set('requestsVisible', $this->Request->find('all', array('conditions' => array(
            'Request.employee_id' => $this->Session->read('Auth.Employee.id'),
            'Request.end_date >=' => date('Y-m-d', strtotime($currentYear . '01-01'))
        ))));
    }

    private function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(),   //array of xml tag names which should always become arrays
            'autoArray' => true,        //only create arrays for tags which appear more than once
            'textContent' => '$',       //key used for the text content of elements
            'autoText' => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch' => false,       //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }
}