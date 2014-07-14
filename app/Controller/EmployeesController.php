<?php
App::uses('CakeEmail', 'Network/Email');
class EmployeesController extends AppController {
    public $helpers = array('Request');
    public $uses = array('Employee', 'User', 'Request');
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow();
    }

    public function index() {
        $this->set('employees', $this->Employee->find('all', array('order' => 'Employee.surname DESC')));
    }

    public function view($id = null) {
        if(isset($this->request->params["named"]["id"])){
            $id = $this->request->params["named"]["id"];
        }

       if($id !== null){
           $this->set('requests', $this->Request->find('all', array('conditions' => array('employee_id' => $id))));
           $this->set('employee', $this->Employee->findById($id));

           if($this->Session->read('Auth.Employee.Role.edituser') == true){

           } elseif($this->Session->read('Auth.Employee.Employee.id') == $id){

           } else{

           }
       }
    }

    public function edit($id = null) {
        $this->set('employee', $this->Employee->findById($id));
        if($this->Session->read('Auth.Employee.Role.edituser') == true){

        } elseif($this->Session->read('Auth.Employee.Employee.id') == $id){

        } else{
            $this->Session->setFlash('Je hebt geen rechten om gebruikers aan te passen');
            //$this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
        }
    }

    public function delete($id = null) {
        $AuthItemController = new AuthItemController();
        if($this->Session->read('Auth.Employee.Role.removeuser') == true){
            $employee = $this->Employee->findById($id);
            $this->User->delete($employee["User"]["id"]);
            $this->Employee->delete($employee["Employee"]["id"]);

        } else{
            $this->Session->setFlash('Je hebt geen rechten om gebruikers te verwijderen');
            //$this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
        }
    }

    public function me(){
        $this->redirect(array('action' => 'view', 'id' => $this->Session->read('Auth.Employee.Employee.id')));
    }

    public function associate(){
        if($this->Session->read('Auth.User.User.status') !== "active"){
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
        } else {
            $this->Session->setFlash('Je bent al gelinkt met een account');
            //$this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
        }
    }

    public function confirmEmail(){

    }

    public function calendar(){

    }
}