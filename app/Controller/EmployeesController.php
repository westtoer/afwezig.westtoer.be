<?php
class EmployeesController extends AppController {

    public $uses = array('Employee', 'User');
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow();
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
        $this->set('users', $this->User->find('all', array('fields' => array('id', 'email', 'name', 'surname', 'group'), 'order' => 'User.id ASC', 'group' => 'User.id')));

    }



    public function edit($id = null) {

    }

    public function delete($id = null) {

    }

    public function me(){

    }

    public function associate(){
            if(isset($this->request->params["named"]["uitid"])){
                if(isset($this->request->params["named"]["assoc"])){
                    $this->redirect(array('controller' => 'employees', 'action' => 'confirmEmail', 'assoc' => $this->request->params["named"]["assoc"], 'uitid' => $this->request->params["named"]["uitid"]));
                } else {
                    $unknownUitId = $this->request->params["named"]["uitid"];
                    $nonActiveEmployees = $this->Employee->find('all', array('conditions' => array('linked' => 0)));
                    $this->set('nonActiveEmployees', $nonActiveEmployees);
                }
            } else {
                $this->redirect(array('controller' => 'users', 'action' => 'error'));
            }
    }

    public function confirmEmail(){

    }
}