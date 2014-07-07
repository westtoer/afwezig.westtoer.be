<?php
App::uses('AppController', 'Controller');
class CalendarItemsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');
    public $uses = array('CalendarItem', 'Employee', 'CalendarItemType', 'EmployeeDepartment');

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        //$this->Auth->allow();
    }

    public function index() {

        $employees = $this->Employee->find('all', array('fields' => array('id', '3gram', 'name', 'surname', 'employee_department_id'), 'order' => 'Employee.id ASC', 'group' => 'Employee.id'));
        $types = $this->CalendarItemType->find('all', array('fields' => array('id', 'name', 'code')));

        if(!empty($CalendarItems)){

        } else {
            $this->redirect(array('action' => 'error'));
        }
        if(!empty($employees)){
            $this->set('employeesOptions', $employeesOptions);
        }

        if(!empty($types)){
            $this->set('typesOptions', $typesOptions);
        }


    }

    public function view($id) {
        if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }

        $post = $this->CalendarItem->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('CalendarItem', $post);
    }

    public function add() {
        //$this->Employee->recursive = -1;
        //$this->set('employees', $this->Employee->find('all', array('fields' => array('id'))));
        if ($this->request->is('post')) {
            $this->CalendarItem->create();

            $incomingData = $this->request->data;
            $incomingData["CalendarItem"]["start_date"] = date('Y-m-d', strtotime($incomingData["CalendarItem"]["start_date"]));
            $incomingData["CalendarItem"]["end_date"] = date('Y-m-d', strtotime($incomingData["CalendarItem"]["end_date"]));

                if(($incomingData["CalendarItem"]["start_date"] == date('Y-m-d', strtotime('1970-01-01'))) or ($incomingData["CalendarItem"]["end_date"] == date('Y-m-d', strtotime('1970-01-01')))){
                    $this->Session->setFlash('Een van de datums waren niet ingevuld.');
                    $this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
                } else {

                    if ($this->CalendarItem->save($incomingData)) {
                        $this->Session->setFlash(__('Your Calendar Item has been saved. Your supervisor and HR will just need to verify and accept.'));
                        return $this->redirect(array('action' => 'index'));
                    }

                    var_dump($this->request->data);
                    $this->Session->setFlash(__('Unable to add your post.'));



             }
        }

    }

    public function edit($id = null) {
        return $this->redirect(array('action' => 'index'));
    }

    public function denied($id) {
        $access = $this->Session->read('Auth.Employee.Role');
        $department = $this->Session->read('Auth.Employee.Employee.employee_department_id');
        if($access["allow"] == true) {
            if($access["adminpanel"] == true){
                //create a Auth item
                $authItem = $this->authConstructor($id, 1, $access, false);
                $this->AuthItem->save($authItem);
                $CalendarItem["CalendarItem"]["approved"] = false;
                $this->CalendarItem->save($CalendarItem);
            } else {
                if($CalendarItem["Employee"]["employee_department_id"] == $department){
                    $authItem = $this->authConstructor($id, 1, $access, false);
                    $this->AuthItem->save($authItem);
                    $CalendarItem["CalendarItem"]["approved"] = true;
                    $this->CalendarItem->save($CalendarItem);
                }
            }
        } else{
            $this->Session->setFlash('Je hebt geen toegang tot deze pagina! Indien dit wel zo is, vraag je toegang aan je systeembeheerder.');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function allow(){
        $access = $this->Session->read('Auth.Employee.Role');
        $department = $this->Session->read('Auth.Employee.Employee.employee_department_id');
        if($access["adminpanel"] == true){
            $this->set('toBeAllowed', $this->Employee->CalendarItem->find('all', array('conditions' => array(
                'CalendarItem.approved' => 0,
                'CalendarItem.start_date >=' => date('Y-m-d')
            ))));
        } elseif($access["allow"] == true){
            $this->set('toBeAllowed', $this->Employee->CalendarItem->find('all', array('conditions' => array(
                'Employee.employee_department_id' => $department),
                'CalendarItem.approved' => 0,
                'CalendarItem.start_date >=' => date('Y-m-d')
            )));
        } else {
            $this->Session->setFlash('Je hebt geen toegang tot deze pagina! Indien dit wel zo is, vraag je toegang aan je systeembeheerder.');
            $this->redirect(array('action' => 'index'));
        }

    }

    public function approved($id){
        $access = $this->Session->read('Auth.Employee.Role');
        $CalendarItem = $this->CalendarItem->find('first', array('conditions' => array('CalendarItem.id' => $id)));
        if($access["allow"] == true) {
            if($access["adminpanel"] == true){
                //create a Auth item
                $authItem = $this->authConstructor($id, 1, $access);
                $this->AuthItem->save($authItem);
                $CalendarItem["CalendarItem"]["approved"] = true;
                $this->CalendarItem->save($CalendarItem);
            } else {
                if($CalendarItem["Employee"]["employee_department_id"] == $this->Session->read('Auth.Employee.Employee.employee_department_id')){
                    $authItem = $this->authConstructor($id, 1, $access);
                    $this->AuthItem->save($authItem);
                    $CalendarItem["CalendarItem"]["approved"] = true;
                    $this->CalendarItem->save($CalendarItem);
                }
            }
        } else{
            $this->Session->setFlash('Je hebt geen toegang tot deze pagina! Indien dit wel zo is, vraag je toegang aan je systeembeheerder.');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function overlap($id){
        if(isset($id)){
            $query = $this->CalendarItem->find('first', array('conditions' => array('CalendarItem.id' => $id)));
            if($query["CalendarItem"]["approved"] !== 1){
                if($query["CalendarItem"]["start_date"] <= date('Y-m-d')){
                    if(!empty($query)){
                        $this->set('query', $query);
                        $this->set('previous', $this->CalendarItem->find('all', array(
                            'limit' => 5,
                            'conditions' => array('Employee.id' => $query["Employee"]["id"], 'CalendarItem.end_date <=' => date('Y-m-d'))

                        )));
                        $this->set('overlap', $this->CalendarItem->find('all', array('conditions' => array(
                            'Employee.id <>' => $query["Employee"]["id"],
                            'CalendarItem.start_date <= ' => date('Y-m-d'),
                            'OR' => array(
                                'CalendarItem.start_date <= ' => date('Y-m-d', strtotime($query["CalendarItem"]["start_date"])),
                                'CalendarItem.end_date <= ' => date('Y-m-d', strtotime($query["CalendarItem"]["end_date"]))
                            )
                        ))));
                    }
                } else {
                    $this->Session->setFlash('Dit verlofitem is al verlopen/gepasseerd.');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash('Dit verlofitem is al goedgekeurd');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }


    public function check(){
        var_dump($this->Session->read('Auth.Employee'));
    }

    private function authConstructor($CalendarItemid, $SupervisorId, $SupervisorAuth, $authorized = true){
        $output["AuthItem"]["calendaritem_id"] = $CalendarItemid;
        $output["AuthItem"]["supervisor_id"] = $SupervisorId;
        $output["AuthItem"]["supervisor_auth"] = $SupervisorAuth;
        $output["AuthItem"]["authorized"] = $authorized;
        $output["AuthItem"]["authorization_date"] = date('Y-m-d H:i:s');
    }


}
