<?php
class AuthItemController extends AppController{
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('');
    }

    /*public function __authConstructor($CalendarItemid, $SupervisorId, $SupervisorAuth, $authorized = true){
        $output["AuthItem"]["calendaritem_id"] = $CalendarItemid;
        $output["AuthItem"]["supervisor_id"] = $SupervisorId;
        $output["AuthItem"]["supervisor_auth"] = $SupervisorAuth;
        $output["AuthItem"]["authorized"] = $authorized;
        $output["AuthItem"]["authorization_date"] = date('Y-m-d H:i:s');
    }*/
}