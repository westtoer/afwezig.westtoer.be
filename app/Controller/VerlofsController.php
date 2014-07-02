<?php
class VerlofsController extends AppController{
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');
    public $uses = array('User');

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow();
    }

    public function index() {

        if(isset($_GET['range'])){
            $range = explode(";", $_GET['range']);
            if(isset($_GET['user'])){
                $user = $_GET['user'];
                if(isset($_GET['group'])){
                    $group = $_GET['group'];
                    $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'OR' => array('Verlof.start >=' => date('Y-m-d H:i:s', strtotime($range[0])), 'Verlof.end <=' => date('Y-m-d H:i:s', strtotime($range[1]))), 'User.id' => $user, 'User.group' => $group))));
                } else{
                $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'OR' => array('Verlof.start >=' => date('Y-m-d H:i:s', strtotime($range[0])), 'Verlof.end <=' => date('Y-m-d H:i:s', strtotime($range[1]))), 'User.id' => $user))));
                }
            } else{
                $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'OR' => array('Verlof.start >=' => date('Y-m-d H:i:s', strtotime($range[0])), 'Verlof.end <=' => date('Y-m-d H:i:s', strtotime($range[1])))))));
            }
        } else if(isset($_GET['user'])){
            $user = $_GET['user'];
            if(isset($_GET['group'])){
                $group = $_GET['group'];
                $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'User.id' => $user, 'User.group' => $group))));
            } else {
                $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'User.id' => $user ))));

            }
        } else if(isset($_GET['group'])){
            $group = $_GET['group'];
            $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1, 'User.group' => $group))));
        } else {
                $this->set('verlofcollectie', $this->User->Verlof->find('all', array('conditions' => array('allowed' => 1))));
            }



        $this->User->unbindModel(
            array('hasMany' => array('Verlof'))
        );
        $this->set('users', $this->User->find('all', array('fields' => array('id', 'email', 'name', 'surname', 'group'), 'order' => 'User.id ASC', 'group' => 'User.id')));
        $this->set('groups', $this->User->find('all', array('fields' => array('group'), 'order' => 'User.group ASC', 'group' => 'User.group')));

    }

    public function view($id) {
        if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }

        $post = $this->Verlof->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('verlof', $post);
    }

    public function add() {
        $this->User->recursive = -1;
        $this->set('users', $this->User->find('all', array('fields' => array('id', 'email', 'name', 'surname', 'group'))));
        if ($this->request->is('post')) {
            $this->User->Verlof->create();
            if ($this->User->Verlof->save($this->request->data)) {
                $this->Session->setFlash(__('Your verlof has been saved. Your manager will just need to verify and accept.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add your post.'));
        }
    }

    public function edit($id = null) {
        return $this->redirect(array('action' => 'index'));
    }

    public function delete($id) {
        $verlof = $this->User->Verlof->find('first', array('conditions' => array('Verlof.id' => $id)));
        if(isset( $verlof["User"]["group"])){
            $groupaccess = 'Auth.User.Acl.' . $verlof["User"]["group"];
        } else{
            $groupaccess = false;
        }
        if($this->Session->read($groupaccess) == true or $this->Session->read('Auth.User.id') == $verlof["User"]["id"]){
            if ($this->User->Verlof->delete($id)) {
                $this->Session->setFlash(
                    __('The post with id: %s has been deleted.', h($id))
                );
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Je hebt geen toelating om dit record aan te passen.'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function aclcheck(){
        $this->User->unbindModel(
            array('hasMany' => array('Verlof'))
        );
        $this->set('usersexpansive', $this->User->find('all', array('fields' => array('Acl.id', 'Acl.user_id', 'Acl.IT', 'ACL.HR'), 'conditions' => array('Acl.user_id' => $this->Session->read('Auth.User.id')))));
    }

    public function allow(){
        $acllist = $this->Session->read('Auth.User.Acl');
        unset($acllist["user_id"]);
        unset($acllist["id"]);
        foreach(array_keys($acllist) as $aclgroup){
            if($this->Session->read('Auth.User.Acl.' . $aclgroup) == true){
                $pendingverlof[$aclgroup] = $this->User->Verlof->find('all', array('fields' => array('User.id', 'User.username', 'User.name', 'User.surname', 'User.group', 'Verlof.id', 'Verlof.start', 'Verlof.end'), 'conditions' => array('Verlof.allowed' => 0, 'User.group' => $aclgroup, 'Verlof.start >=' => date('Y-m-d'))));
                $pendingverlof[$aclgroup]["title"] = $aclgroup;
            }

        }

        $this->set('pending', $pendingverlof);
    }

    public function approved($id){
        $verlof = $this->User->Verlof->find('first', array('conditions' => array('Verlof.id' => $id)));
        $groupaccess = 'Auth.User.Acl.' . $verlof["User"]["group"];
        if($this->Session->read($groupaccess) == true){
            $verlof["Verlof"]["allowed"] = true;
            $this->User->Verlof->save($verlof);
            $this->Session->setFlash(__('Het verlof werd goedgekeurd.'));
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Je hebt geen toelating om dit record aan te passen.'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function overlap($id){
        $baseverlof = $this->User->Verlof->find('first', array('conditions' => array('Verlof.id' => $id)));
        $this->set('searchquery', $baseverlof);
        if(isset($this->request->query['group'])){
            $overlappingverlofs = $this->User->Verlof->find('all', array('conditions' => array('User.id <>' => $this->Session->read("Auth.User.id"), 'group' => $this->request->query['group'],'Verlof.start <=' => date('Y-m-d H:i:s', strtotime($baseverlof["Verlof"]["start"])), 'Verlof.end <=' => date('Y-m-d H:i:s', strtotime($baseverlof["Verlof"]["end"])))));
        } else {
            $overlappingverlofs = $this->User->Verlof->find('all', array('conditions' => array('User.id <>' => $this->Session->read("Auth.User.id"), 'Verlof.start <=' => date('Y-m-d H:i:s', strtotime($baseverlof["Verlof"]["start"])), 'Verlof.end <=' => date('Y-m-d H:i:s', strtotime($baseverlof["Verlof"]["end"])))));

        }


        foreach ($overlappingverlofs as $overlappingverlof){
            if($overlappingverlof["Verlof"]["id"] !==  $id){

            } else {
                $output[] = $overlappingverlof;
                if(count($output) > 0) {
                   $this->set('output', $output);
                }

            }
        }
        $this->User->unbindModel(
            array('hasMany' => array('Verlof'))
        );
        $groups = $this->User->find('all', array('fields' => array('group'), 'order' => 'User.group ASC', 'group' => 'User.group'));
        $this->set('groups', $groups);
    }


}
