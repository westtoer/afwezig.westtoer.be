<?php
class AdminController extends AppController {
    public $uses = array('User', 'Employee', 'Request', 'EmployeeDepartment', 'AdminBookingdate');
    public $helpers = array('Employee', 'Request');

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        if($this->Session->read('Auth.Role.id') !== '1'){

            $this->Session->setFlash('Je hebt geen rechten in het administratiepaneel.');
            $this->redirect('/');

        } else if($this->Session->read('Auth.Role.id') !== '2') {

        };
    }

    public function index(){

    }
    //General settings
    public function maintenanceMode(){

    }

    //Admin section for Employees
    public function registerEmployee(){
        $this->set('departments', $this->EmployeeDepartment->find('all'));
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.id <>' => 4, 'Employee.linked' => 1))));
        $this->set('');
        if($this->request->is('post')){

            $this->Employee->create();
            $employee = $this->request->data;
            var_dump($employee);
            //$this->Employee->save($employee);
        }
    }

    public function viewEmployees(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.id <>' => 4), 'order' => 'Employee.name ASC')));

    }

    public function viewRegistrations(){
        $this->set('registrations', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
    }

    public function viewUsers(){
        $this->set('usersActive', $this->User->find('all', array('conditions' => array('User.status' => 'active'))));
        $this->set('usersPending', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
        $this->set('usersDenied', $this->User->find('all', array('conditions' => array('User.status' => 'denied'))));
    }

    public function Roles(){
        $this->set('employees', $this->Employee->find('all'));
        if(isset($this->request->params["named"]["id"])){
            if(isset($this->request->params["named"]["action"])){
                $employee = $this->Employee->findById($this->request->params["named"]["id"]);
                if($this->request->params["named"]["action"] == 'admin'){
                    $employee["Employee"]["role_id"] = 1;
                } elseif($this->request->params["named"]["action"] == 'hr'){
                    $employee["Employee"]["role_id"] = 2;
                } elseif($this->request->params["named"]["action"] == 'supervisor'){
                    $employee["Employee"]["role_id"] = 4;
                } elseif($this->request->params["named"]["action"] == 'standard'){
                    $employee["Employee"]["role_id"] = 3;
                }

                $this->Employee->save($employee);
            }
        } else {
            $this->redirect(array('controller' => 'admin', 'action' => 'viewEmployees'));
        }
    }

    //Admin section for Calendar Items
    public function viewPendingCalendarItems(){
       $this->set('toBeAllowed', $this->Request->find('all', array('conditions' => array(
            'AuthItem.authorized' => 0,
            'AuthItem.authorization_date' => null,
            'Request.start_date >=' => date('Y-m-d')
        ))));
    }

    public function lockDates(){

    }

    public function viewEditsAfterLock(){
        $this->set('edits', $this->editsAfterLock());

    }

    public function pdfEditsAfterLock(){
        if(!$this->request->params["named"]["month"]){
            $instance = date('Y-m', strtotime($this->request->params["named"]["month"] . '/01/' . date('Y')));
        } else {
            $instance = date('Y-m');
        }
    }

    public function GeneralCalendarItems(){
        $this->set('requests', $this->Request->find('all', array(
            'conditions' => array(
                'Request.employee_id' => 4,
                'Request.calendar_item_type_id' => 3,
            )
        )));
        if($this->request->is('post')){
            $request = $this->request->data;
                $request["Request"]["employee_id"] = 4;
                $request["Request"]["calendar_item_type_id"] = 3;
                $request["Request"]["timestamp"] = date('Y-m-d H:i:s');
                $request["Request"]["replacement_id"] = 4;
                $request["Request"]["auth_item_id"] = 4;
                $this->Request->create();
                $this->Request->save($request);

        } elseif(isset($this->request->params["named"]["id"])){
            if(isset($this->request->params["named"]["action"])){
                if($this->request->params["named"]["action"] == 'delete'){
                    $request = $this->Request->findById($this->request->params["named"]["id"]);
                    if(!empty($request)){
                        $this->Request->delete($request["Request"]["id"]);
                        $this->Session->setFlash('Deze algemene feestdag is verwijderd.');
                        $this->redirect(array('controller' => 'admin', 'action' => 'GeneralCalendarItems'));
                    }
                }
            }
        }
    }

    public function endOfYear(){
        //Wizard
    }



    //Admin section for Reports
    public function generateReport(){

    }

    public function lookupHistory(){

    }

    public function sendReport(){

    }

    private function editsAfterLock(){

        if(!isset($this->request->params["named"]["month"])){
        $conditions = array('month' => date('m'));
        } else{
            $conditions = array('month' => $this->request->params["named"]["month"]);
        }

        $bookingDate = $this->AdminBookingdate->find('first', array('conditions' => $conditions));
        if(!empty($bookingDate)){
            $edits =  $this->Request->find('all', array('conditions' =>
                array(
                    'Request.timestamp >=' => date('Y-m-d H:i:s', strtotime($bookingDate["AdminBookingdate"]["month"] . '/' .$bookingDate["AdminBookingdate"]["day_of_month"] .  $bookingDate["AdminBookingdate"]["year"] . ' 00:00:00')),
                    'Request.timestamp <=' => date('Y-m-d H:i:s', strtotime($bookingDate["AdminBookingdate"]["month"] . '/' . 31 . '/' . $bookingDate["AdminBookingdate"]["year"] . ' 00:00:00')),
                    'AuthItem.authorized' => 1,
                )));
        } else {
            $this->Session->setFlash('Je moet eerst de wizard voor het begin van het jaar doorlopen.');
            $this->redirect(array('controller' => 'Admin', 'action' => 'index'));
        }

        return $edits;
    }

    public function icaltest(){
        $myFile = "/var/www/html/afwezig.westtoer.be/webroot/files/test.ical";
        $fh = fopen($myFile, 'w') or die("can't open file");
        $stringData = "Bobby Bopper\n";
        fwrite($fh, $stringData);
        $stringData = "Tracy Tanner\n";
        fwrite($fh, $stringData);
        fclose($fh);

    }
}