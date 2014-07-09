<?php
App::uses('AppController', 'Controller');
class CalendarItemsController extends AppController {

    //load dependancies
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'Paginator');
    public $uses = array('CalendarItem', 'Employee', 'CalendarItemType', 'EmployeeDepartment');

    //configure dependancies
    public $paginate = array('limit' => 20);

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        //$this->Auth->allow();
    }

    //page functions
    public function index() {
        //Fetching all data
            $employees = $this->Employee->find('all', array('fields' => array('id', '3gram', 'name', 'surname', 'employee_department_id'), 'order' => 'Employee.id ASC', 'group' => 'Employee.id'));
            $types = $this->CalendarItemType->find('all', array('fields' => array('id', 'name', 'code')));
            $CalendarItemsToday = $this->CalendarItem->find('all', array('conditions' => array('CalendarItem.start_date <= ' => date('Y-m-d'), 'CalendarItem.end_date >=' => date('Y-m-d'))));

            $this->CalendarItem->unbindModel(array('belongsTo' => array('Employee')));
                $CalendarItemsGlobal = $this->CalendarItem->find('all', array('conditions' => array('CalendarItem.employee_id' => 0, 'CalendarItem.replacement_id' => 0), 'fields' => array('CalendarItem.note', 'CalendarItem.start_date', 'CalendarItem.start_time', 'CalendarItem.end_date', 'CalendarItem.end_time')));
            $employeeDepartments = $this->EmployeeDepartment->find('all');

        if(!empty($CalendarItemsToday)){
            $this->set('CalendarItemsToday', $CalendarItemsToday);
        }
        if(!empty($employees)){
            $this->set('employees', $employees);
            if(!empty($employeeDepartments)){
                $this->set('employeeDepartments', $employeeDepartments);
            }
        }

        if(!empty($types)){
            $this->set('types', $types);
        }

        if(!empty($CalendarItemsGlobal)){
            $this->set('CalendarItemsGlobal', $CalendarItemsGlobal);
        }

        //Set standard options
        $conditions[] = array('CalendarItem.approved' => 1);

        if(isset($this->request->query["range"])){
            $range = explode(';', $this->request->query["range"]);
            $conditions[] = array('CalendarItem.start_date >=' => date('Y-m-d', strtotime($range[0]) ), 'CalendarItem.end_date <=' => date('Y-m-d', strtotime($range[1])));
        } else {
            $conditions[] = array('CalendarItem.start_date >' => date('Y-m-d'), 'CalendarItem.end_date <=' => date('Y-m-d', strtotime("+6 Days")));
        }

        if(isset($this->request->query["user"])){
            $conditions[] = array('CalendarItem.employee_id =' => $this->request->query["user"]);
        }

        if(isset($this->request->query["group"])){
            $conditions[] = array('Employee.employee_department_id =' => $this->request->query["group"]);
        }
        $CalendarItemsWeek = $this->CalendarItem->find('all', array('conditions' => $conditions));
        if(!empty($CalendarItemsWeek)){
            $this->set('CalendarItemsWeek', $CalendarItemsWeek);
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
            $errortext = '';
            $incomingData = $this->request->data;
            $incomingData["CalendarItem"]["start_date"] = date('Y-m-d', strtotime($incomingData["CalendarItem"]["start_date"]));
            $incomingData["CalendarItem"]["end_date"] = date('Y-m-d', strtotime($incomingData["CalendarItem"]["end_date"]));



            //validating the dates
                if(($incomingData["CalendarItem"]["start_date"] == date('Y-m-d', strtotime('1970-01-01'))) or ($incomingData["CalendarItem"]["end_date"] == date('Y-m-d', strtotime('1970-01-01')))){
                    $errortext .= 'Een van de datums waren niet ingevuld. <br />';
                } elseif($incomingData["CalendarItem"]["start_date"] > $incomingData["CalendarItem"]["end_date"] ){
                    $errortext .= 'De startdatum kan niet later zijn in de tijd dan de einddatum. <br />';
                } elseif($incomingData["CalendarItem"]["start_date"] < date('Y-m-d')) {
                    $errortext .= 'De startdatum kan niet retroactief zijn.';
                }

            //validating the CalendarItemType
                if($incomingData["CalendarItem"]["calendar_item_type_id"] == 0){
                    $errortext .= 'U hebt geen geldig verloftype gekozen <br />';
                }

                if($errortext == ''){
                    if ($this->CalendarItem->save($incomingData)) {
                        $this->Session->setFlash(__('Your Calendar Item has been saved. Your supervisor and HR will just need to verify and accept.'));
                        return $this->redirect(array('action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash($errortext);
                    return $this->redirect(array('action' => 'index'));
                }


        }

    }

    public function edit($id = 0) {
        if($id !== 0){
            $employee = $this->Employee->findById($this->Session->read('Auth.Employee.Employee.id'));
            $CalendarItem = $this->CalendarItem->findById($id);
            $this->set('employees', $this->Employee->find('all'));
            if(!empty($employee) and !empty($CalendarItem)){
                if($CalendarItem["Employee"]["id"] == $employee["Employee"]["id"] or $this->Session->read('Auth.Employee.Role.editcalendaritem') == true){
                    $this->set('employee', $employee);
                    $this->set('CalendarItem', $CalendarItem);
                } else {
                    $this->Session->setFlash('Je hebt geen toegang om dit aan te passen');
                    $this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
                }
            } else {
                $this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
            }
        } else {
                $this->redirect(array('controller' => 'CalendarItems', 'action' => 'index'));
        }
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
        } if($access["allow"] == true){
            $this->set('toBeAllowed', $this->Employee->CalendarItem->find('all', array('conditions' => array(
                'Employee.employee_department_id' => $department,
                'CalendarItem.approved' => 0,
                'CalendarItem.start_date >=' => date('Y-m-d')
            ))));
        } else {
            $this->Session->setFlash('Je hebt geen toegang tot deze pagina! Indien dit wel zo is, vraag je toegang aan je systeembeheerder.');
            $this->redirect(array('action' => 'index'));
        }

    }

    public function approved($id){
        $access = $this->Session->read('Auth.Employee.Role');
        $CalendarItem = $this->CalendarItem->find('first', array('conditions' => array('CalendarItem.id' => $id)));
        $AuthItemController = new AuthItemController();
        if($access["allow"] == true) {
            if($access["adminpanel"] == true){
                //create a Auth item
                $authItem = $AuthItemController->authConstructor($id, 1, $access);
                $this->AuthItem->save($authItem);
                $CalendarItem["CalendarItem"]["approved"] = true;
                $this->CalendarItem->save($CalendarItem);
            } else {
                if($CalendarItem["Employee"]["employee_department_id"] == $this->Session->read('Auth.Employee.Employee.employee_department_id')){
                    $authItem = $AuthItemController->__authConstructor($id, 1, $access);
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
                if($query["CalendarItem"]["start_date"] >= date('Y-m-d')){
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
        var_dump($this->Session->read('Auth.Employee.Role.editcalendaritem'));
        echo '<hr />';
        var_dump($this->Session->read('Auth.User'));
    }




}
