<?php
App::import('Vendor', 'OAuth/OAuthClient');
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
        $requestToken = $client->getRequestToken('http://acc.uitid.be/uitid/rest/requestToken', 'http://' . $_SERVER["HTTP_HOST"] . $this->base .'/users/callback');
        if (!empty($requestToken)) {
            $this->Session->write('uitid_request_token', $requestToken);
            $this->redirect('http://acc.uitid.be/uitid/rest/auth/authorize?oauth_token=' . $requestToken->key);
        } else {

        }
    }

    public function callback() {
        $requestToken = $this->Session->read('uitid_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('http://acc.uitid.be/uitid/rest/accessToken', $requestToken);
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
                    //$this->redirect('http://acc.uitid.be/uitid/rest/user/' . $accessToken->userId . '?private=true');

                    //$report = http_get('http://acc.uitid.be/uitid/rest/user/' . $accessToken->userId);

                    //OAUTH TEST






                    $this->redirect('/');
                } else {
                    $this->redirect(array('action' => 'error'));
                }
            } else {
                $result = $client->request($accessToken->key, $accessToken->secret, $accessToken->userId, array('method' => 'GET', 'uri' => 'http://acc.uitid.be/uitid/rest/user/' . $accessToken->userId . '?private=true'));
                $resultToArray =  preg_split ('/$\R?^/m', $result["body"]);
                $result = $client->processResult($resultToArray);
                $this->redirect(array("controller" => "Employees", "action" => "associate", 'uitid' => base64_encode($accessToken->userId), 'email' => base64_encode($result["email"])));
            }
        }
    }

    public function error(){
        //$this->layout = 'login';

    }


    private function createClient() {
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
            $this->User->save($newAssociation);
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
            $access = '';
            if($access == 'verifyuser'){
                $useraccount = $this->User->findById($id);
                $employeeaccount = $this->User->findById($useraccount["Employee"]["id"]);

                if(!empty($useraccount)){
                    if(!empty($employeeaccount)){
                        $useraccount["User"]["status"] = 'active';
                        $employeeaccount["Employee"]["linked"] = 1;

                        //sets the User Role to standard, so people wont register account based on their influence
                        $employeeaccount["Employee"]["role_id"] = 3;

                        $this->User->save($useraccount);
                        $this->Employee->save($employeeaccount);
                    }
                }
            }
        }
    }

    public function deny($id = null){
        if(isset($id)){
            $access = '';
            if($access == 'verifyuser'){
                $useraccount = $this->User->findById($id);
                $employeeaccount = $this->User->findById($useraccount["Employee"]["id"]);

                if(!empty($useraccount)){
                    if(!empty($employeeaccount)){
                        $useraccount["User"]["status"] = 'denied';
                        $employeeaccount["Employee"]["linked"] = 0;
                        $this->User->save($useraccount);
                        $this->Employee->save($employeeaccount);
                    }
                }
            }
        }
    }

    public function unlink($id = null){
        if(isset($id)){
            $useraccount = $this->User->findById($id);
            $employee = $this->Employee->findById($useraccount["Employee"]["id"]);
            if($this->Session->read('Auth.Employee.id') == $employee["Employee"]["id"]){
                $this->User->delete($useraccount["User"]["id"]);
                $this->Session->destroy();
                $this->Session->setFlash('De werknemersgegevens werden losgekoppelt van de aanmeldgegevens');
                $this->redirect(array('action' => 'login'));
            } elseif($this->Session->read('Auth.User.Role.edituser') == true){

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
}