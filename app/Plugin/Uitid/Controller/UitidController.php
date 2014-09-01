<?php
App::import('Vendor', 'Uitid.OAuth/OAuthClient');
class UitidController extends UitidAppController{

    public $uses = array('User', 'Employee');

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow();
    }

    public function index(){

    }

    //Fetches the application settings
    private function createClient() {
            return new OAuthClient(Configure::read('Uitid.public'), Configure::read('Uitid.private'));
    }

    //Redirects to the UiTID service
    public function uitid() {
            $client = $this->createClient();
            $requestToken = $client->getRequestToken(Configure::read('Uitid.server') . '/requestToken', 'http://' . $_SERVER["HTTP_HOST"] . $this->base .'/uitid/callback');
            if (!empty($requestToken)) {
                $this->Session->write('uitid_request_token', $requestToken);
                $this->redirect(Configure::read('Uitid.server') . '/auth/authorize?oauth_token=' . $requestToken->key);
            } else {
                //Throw an error
            }
        }

        public function callback() {
            $requestToken = $this->Session->read('uitid_request_token');
            $client = $this->createClient();
            $accessToken = $client->getAccessToken(Configure::read('Uitid.server') .'/accessToken', $requestToken);
            if ($accessToken) {
                $user = $this->User->find('first', array('conditions' => array('User.uitid' => $accessToken->userId)));
                if(!empty($user)){
                    if($user["User"]["status"] == 'active'){
                        $employee = $this->Employee->find('first',
                            array('conditions' => array('Employee.id' => $user["User"]["employee_id"]),
                                'fields' => array('Employee.id', 'Employee.status', 'Employee.internal_id', 'Employee.employee_department_id', 'Employee.Name', 'Employee.surname', 'Role.id', 'Role.name', 'Role.adminpanel', 'Role.allow', 'Role.verifyuser', 'Role.edituser', 'Role.removeuser', 'Role.editcalendaritem')
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
                    $result = $client->request($accessToken->key, $accessToken->secret, $accessToken->userId, array('method' => 'GET', 'uri' =>  Configure::read('Uitid.server')  .'/user/' . $accessToken->userId . '?private=true'));
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
         }
}