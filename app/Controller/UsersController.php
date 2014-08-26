<?php
App::import('Vendor', 'OAuth/OAuthClient', 'DryXML/DryXML.php');
App::uses('CakeEmail', 'Network/Email');
class UsersController extends AppController {
    public $uses = array('User', 'Employee', 'Request', 'EmployeeDepartment');
    public $helpers = array('Request');
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('login', 'logout', 'uitid', 'callback', 'error', 'associate', 'requestHandled', 'emergencyLogin', 'locked');
    }

    public function login() {
            $this->layout = 'login';
            if(isset($this->request->query['router'])){
                $this->set('router', $this->request->query['router']);

            }
            if($this->Auth->loggedIn()){
                if(isset($this->request->query['router'])){
                    $this->redirect($this->request->query['router']);
                } else {
                    $this->redirect('/');
                }
            }
            else {
                if ($this->request->is('post')) {
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
        if(isset($this->request->query['router'])){
            $this->Session->write('router', $this->request->query['router']);
        }

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
                    $employee = $this->Employee->find('first',
                        array('conditions' => array('Employee.id' => $user["User"]["employee_id"]),
                            'fields' => array('Employee.id', 'Employee.employee_department_id', 'Employee.Name', 'Employee.surname', 'Role.id', 'Role.name', 'Role.adminpanel', 'Role.allow', 'Role.verifyuser', 'Role.edituser', 'Role.removeuser', 'Role.editcalendaritem')
                        ));
                    if(!empty($employee)){
                        if($employee["Employee"]["status"] == true){
                            $this->Auth->login($user['User']['id']);
                            $this->Session->write('Auth', $employee);
                            $this->Session->write('Auth.User', $user);

                            if($this->Session->read('router') == null){
                                $this->redirect('/');

                            } else {
                                $this->redirect($this->Session->read('router'));
                            }
                        } else {
                            $this->redirect(array('action' => 'deactivatedEmployee'));
                        }
                    }

                } else {
                    if($user["User"]["status"] == 'deactivated'){
                        $this->redirect(array('action' => 'deactivatedUser'));
                    }
                    $this->redirect(array('action' => 'error'));
                }
            } else {
                $result = $client->request($accessToken->key, $accessToken->secret, $accessToken->userId, array('method' => 'GET', 'uri' =>  Configure::read('UiTID.server')  .'/user/' . $accessToken->userId . '?private=true'));
                $resultToArray = simplexml_load_string($result["body"]);
                $resultToArray = json_decode(json_encode($this->xmlToArray($resultToArray)), 1);
                //$result = $client->processResult($resultToArray);
                if($this->Employee->find('count') > 1){
                    $this->redirect(array("controller" => "Employees", "action" => "associate", 'uitid' => base64_encode($accessToken->userId), 'email' => base64_encode($resultToArray["person"]["foaf:mbox"])));
                } else {
                    $this->redirect(array("controller" => "Employees", "action" => "claimAdmin", 'uitid' => base64_encode($accessToken->userId), 'email' => base64_encode($resultToArray["person"]["foaf:mbox"])));
                }
            }
        }
    }

    public function error(){
        $this->layout = 'login';
    }

    public function deactivatedUser(){
        $this->layout = 'login';
    }

    public function deactivatedEmployee(){
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

            //$this->Session->setFlash(__('The user has been saved'));
            $this->sendMailToHR();
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
                                    $this->sendMail($this->trigramToMail($useraccount["Employee"]["3gram"]), 'Je bent toegelaten op Westtoer Afwezig. Vanaf nu kun je gewoon surfen naar ' . Configure::read('Administrator.base_fallback_url') . ' en je aanmelden met je UiTID.');
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

                            $this->Session->setFlash('Je hebt toegang geweigerd tot het systeem aan ' . $useraccount["User"]["email"]);
                            $this->sendMail($this->trigramToMail($useraccount["Employee"]["3gram"]), 'Je bent niet toegelaten op Westtoer Afwezig');
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

    public function management(){
        $this->set('employee', $this->Employee->findById($this->Session->read('Auth.Employee.id')));
        $this->set('departments', $this->EmployeeDepartment->find('all'));
        $currentYear = date("Y");
        $this->set('linkedUsers', $this->User->find('all', array('conditions' => array(
            'User.employee_id' => $this->Session->read('Auth.Employee.id')
        ))));
        $this->set('requestsVisible', $this->Request->find('all', array('conditions' => array(
            'Request.employee_id' => $this->Session->read('Auth.Employee.id'),
            'Request.end_date >=' => date('Y-m-d', strtotime($currentYear . '01-01'))
        ))));
    }

    public function claimAdmin(){
        if($this->Employee->find('count') > 1){
            if($this->User->find('count') > 0){
                if($this->request->is('post')){

                    $this->Employee->create();
                    $incomingData = $this->request->data;
                    $employeeTemplate = array('Employee' => array('role_id' => 1, 'telephone' => '0000', 'note' => '', 'daysleft' => 4, 'status' => '1', 'supervisor_id' => '-1', 'gsm' => '0'));
                    $employee = array_merge($incomingData, $employeeTemplate);
                    $employee = $this->Employee->save($employee);

                    $this->User->create();
                    $userTemplate = array('User' => array('employee_id' => $employee["Employee"]["id"], 'email' => base64_decode($this->request->query['email'])), 'uitid' => base64_decode($this->request->query['uitid']), 'status' => 'active');
                    $this->User->save($userTemplate);

                    $this->redirect(array('controller' => 'users', 'action' => 'login'));

                } else {
                    $this->redirect('/');
                }
            } else {
                $this->redirect('/');
            }
        } else {
            $this->redirect('/');
        }
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

    public function checkAuth(){
        echo '<pre>';
        var_dump($this->Employee->find('all'));
        echo '</pre>';
        var_dump($this->Employee->findById("1"));
    }
    public function locked(){
        $this->layout = 'login';
    }

    private function sendMailToHR($type = "newuser"){
        $allHR = $this->Employee->find('all', array('conditions' => array('Employee.role_id <' => 3)));
        foreach($allHR as $HR){
            $Email = new CakeEmail('westtoer');
            $Email->to($this->trigramToMail($HR["Employee"]["3gram"]));
            $Email->subject('Er is een nieuwe gebruiker geregistreerd op Westtoer Afwezig.');
            $Email->replyTo('noreply@westtoer.be');
            $Email->from ('noreply@westtoer.be');
            $Email->send('Er is een nieuwe gebruiker die zich heeft aangemeld op Afwezig. Om dit te bekijken, ga je naar http://afwezig.westtoer.be/Admin/ViewRegistrations');
        }
    }

    private function trigramToMail($trigram){
        if(strpos($trigram, '@')){
            return $trigram;
        } else {
            return $trigram . '@westtoer.be';
        }
    }

    private function sendMail($receiver, $body, $subject = "Westtoer Afwezig"){
        if(strpos($receiver, '@') !== false){
            if(strpos($receiver, 'westtoer.be') !== false){
                $Email = new CakeEmail('westtoer');
                $Email->to($receiver);
                $Email->subject($subject);
                $Email->replyTo('noreply@westtoer.be');
                $Email->from ('noreply@westtoer.be');
                $Email->send($body);
            }
        }
    }
}