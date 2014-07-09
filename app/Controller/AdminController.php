<?php
class AdminController extends AppController {

    public $uses = array('User', 'Employee', 'CalendarItem');

    public function index(){

    }
    //General settings
    public function maintenanceMode(){

    }

    //Admin section for Employees
    public function employeeRegister(){

    }

    public function viewRegistrations(){
        $this->set('registrations', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
    }

    public function viewUsers(){
        $this->set('usersActive', $this->User->find('all', array('conditions' => array('User.status' => 'active'))));
        $this->set('usersPending', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
        $this->set('usersDenied', $this->User->find('all', array('conditions' => array('User.status' => 'denied'))));
    }

    public function changeRoles(){

    }

    public function assignRoles(){

    }
    //Admin section for Calendar Items
    public function viewPendingCalendarItems(){
        $this->set('toBeAllowed', $this->Employee->CalendarItem->find('all', array('conditions' => array(
            'CalendarItem.approved' => 0,
            'CalendarItem.start_date >=' => date('Y-m-d')
        ))));
        $this->set('employees', $this->Employee->find('all'));
    }

    public function lockDate(){

    }

    public function viewEditsAfterLock(){

    }

    public function addGeneralCalendarItems(){

    }

    public function endOfYear(){

    }

    public function viewEmployeesCalendarItems(){

    }

    //Admin section for Reports
    public function generateReport(){

    }

    public function lookupHistory(){

    }

    public function sendReport(){

    }
}