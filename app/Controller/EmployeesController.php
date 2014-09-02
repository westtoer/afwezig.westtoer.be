<?php
App::uses('CakeEmail', 'Network/Email');
class EmployeesController extends AppController {
    public $helpers = array('Request');
    public $uses = array('Employee', 'User', 'Request', 'CalendarDay');
    public $components = array('Csv');

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('associate', 'confirmEmail', 'claimAdmin');
        $this->set('title_for_layout', 'Westtoer Afwezig - Werknemers');
    }

    public function index() {
        $this->set('employees', $this->Employee->find('all', array('order' => 'Employee.surname DESC')));
    }

    public function view($id = null) {
        if($id == "me"){
            $id = $this->Session->read('Auth.Employee.id');
        } elseif(isset($this->request->params["named"]["id"])){
            $id = $this->request->params["named"]["id"];
        }



       if($id !== null){
           $employee = $this->Employee->findById($id);
           $this->set('requests', $this->Request->find('all', array('conditions' => array('employee_id' => $id), 'order' => 'Request.timestamp DESC')));
           $this->set('employee', $employee);

           if($id == $this->Session->read('Auth.Employee.id')){
               $show = true;
               $daysleft = ($employee["Employee"]["daysleft"] - $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.employee_id' => $employee["Employee"]["id"], 'CalendarDay.calendar_item_type_id' => 23)))) / 2;
               $this->set('daysleft', $daysleft);
           } else {
               $show = false;
           }

           $this->set('show', $show);

           if($this->Session->read('Auth.Employee.Role.edituser') == true){

           } elseif($this->Session->read('Auth.Employee.Employee.id') == $id){

           } else{

           }
       }
    }

    public function delete($id = null) {
        $AuthItemController = new AuthItemController();
        if($this->Session->read('Auth.Employee.Role.removeuser') == true){
            $employee = $this->Employee->findById($id);
            $this->User->delete($employee["User"]["id"]);
            $this->Employee->delete($employee["Employee"]["id"]);

        } else{
            $this->Session->setFlash('Je hebt geen rechten om gebruikers te verwijderen', 'default', array('class' => 'alert-danger'));
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
                    $this->redirect(array('controller' => 'employees', 'action' => 'confirmEmail', 'assoc' => $this->request->params["named"]["assoc"], 'uitid' => $this->request->params["named"]["uitid"], 'email' => $this->request->params["named"]["email"]));
                } else {
                    $unknownUitId = $this->request->params["named"]["uitid"];
                    $nonActiveEmployees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1', 'Employee.status' => 1), 'order' => 'Employee.name ASC'));
                    $this->set('nonActiveEmployees', $nonActiveEmployees);
                }
            } else {
                $this->redirect(array('controller' => 'users', 'action' => 'error'));
            }
        } else {
            $this->Session->setFlash('Je bent al gelinkt met een account', 'default', array('class' => 'alert-danger'));
            //$this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
        }
    }

    public function confirmEmail(){

    }

    public function calendar(){

    }

    public function claimAdmin(){

    }

    public function edit(){
        if($this->request->is('post')){
            $employeeRequested = $this->request->data;
            var_dump($employeeRequested);
            if($this->Session->read('Auth.Employee.id') !== $employeeRequested["Employee"]["id"]){
                if($this->Session->read('Auth.Role.adminpanel') !== true){
                } else {
                    $this->Session->setFlash('Je mag een andere gebruiker niet aanpassen.', 'default', array('class' => 'alert-danger'));
                    $this->redirect('/');
                }
            } else { // The user is updating his information
                $employee = $this->Employee->findById($employeeRequested["Employee"]["id"]);
                $employee["Employee"]["note"] = $employeeRequested["Employee"]["note"];
                $employee["Employee"]["telephone"] = $employeeRequested["Employee"]["telephone"];
                $employee["Employee"]["gsm"] = $employeeRequested["Employee"]["gsm"];
                $this->Employee->save($employee);
                $this->Session->setFlash('Je hebt je gegevens aangepast.');
                $this->redirect(array('controller' => 'users', 'action' => 'management'));
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'management'));
        }
    }

    public function import() {
        if($this->Session->read('Auth.Role.adminpanel') == true){
            if($this->request->is('post')){
                $data = $this->Csv->import($this->request->data["Employee"]["CsvFile"]["tmp_name"]);
                $this->Employee->create();
                foreach($data as $key => $datarecord){
                    $employee = $this->Employee->find('all', array('conditions' => array('Employee.name' => $datarecord["name"], 'Employee.surname' => $datarecord["surname"], 'Employee.internal_id' => $datarecord["internal_id"])));
                    if(!empty($employee)){
                        unset($data[$key]);
                    } else {
                        $datarecord["Employee"]["role_id"] = 3;
                        if(!isset($datarecord["Employee"]["department_id"])){
                            $datarecord["Employee"]["department_id"] = 1;
                        }
                        $datarecord["Employee"]["status"] = 1;
                        if(!isset($datarecord["Employee"]["status"])){
                            $datarecord["Employee"]["supervisor_id"] = '-1';
                        }
                        $cleanData[] = $datarecord;
                    }
                    unset($employee);

                }

                if(!empty($cleanData)){
                    if($this->Employee->saveMany($cleanData)){
                        $this->Session->setFlash('Het importeren is geslaagd');
                    } else {
                        $this ->Session->setFlash('Er is iets misgelopen. Waarschijnlijk heb je dubbele werknemers of werknemers die al in het systeem zitten proberen toevoegen.', 'default', array('class' => 'alert-danger'));
                    }
                } else {
                    $this->Session->setFlash('Er was geen data om te importeren.', 'default', array('class' => 'alert-danger'));
                }

                $this->redirect($this->here);
            }
        } else {
            $this->redirect('/');
        }

    }


    public function checkSession(){
        $supervisor = $this->Employee->findById($this->Session->read('Auth.Employee.id'))["Supervisor"];
        var_dump($supervisor);
        echo '<hr />';
        $employee = $this->Employee->findById($this->Session->read('Auth.Employee.id'))["Employee"];
        var_dump($employee);
        echo '<hr />';
        $expected = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $employee["supervisor_id"])));
        var_dump($expected);
    }
}