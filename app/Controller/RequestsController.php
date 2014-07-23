<?php
App::uses('CakeEmail', 'Network/Email', 'AppController', 'Controller');
class RequestsController extends AppController {

    public $uses = array('Employee', 'Request', 'AuthItem', 'CalendarDay', 'RequestToCalendarDay', 'CalendarItemType');
    public $helpers = array('Employee');
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        //$this->Auth->allow();
        $the_session = $this->Session->read('Auth');
        if(empty($the_session)){
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
    }

    //page functions
    public function index() {
        $access = $this->Session->read('Auth.Role');
        $supervisor = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
        if($access["allow"] == true){
            if($supervisor["Role"]["name"] == 'admin'){
                $conditions = array('AuthItem.authorized' => false, 'AuthItem.authorization_date' => null, 'Request.start_date >=' => date('Y-m-d'));
            } else {
                $conditions = array('AuthItem.authorized' => false, 'AuthItem.authorization_date' => null, 'Employee.supervisor_id' => $supervisor["Employee"]["id"], 'Request.start_date >=' => date('Y-m-d'));
            }
            $this->set('requests', $this->Request->find('all', array('conditions' => $conditions, 'order' => 'Request.timestamp ASC')));
        }
    }

    public function view($id = null) {
        if($id !== null){
            $access = $this->Session->read('Auth.Role.allow');
            if($access == true){
                $query = $this->Request->findById($id);
                if($query["AuthItem"]["authorization_date"] == null){
                    if($query["AuthItem"]["authorized"] !== true){
                        if($query["Request"]["start_date"] >= date('Y-m-d')){
                            $this->set('query', $query);
                            $previous = $this->Request->find('all', array(
                                'conditions' => array(
                                    'AuthItem.authorized' => 1
                                ), 'limit' => 5,
                            ));
                            $overlap = $this->Request->find('all', array(
                                'conditions' => array(
                                    'OR' => array(
                                        'Request.start_date <= ' => date('Y-m-d', strtotime($query["Request"]["start_date"])),
                                        'Request.end_date <= ' => date('Y-m-d', strtotime($query["Request"]["end_date"]))
                                    ),
                                    'OR' => array(
                                        'AuthItem.authorized' => true,
                                        'AuthItem.authorization_date' => null
                                    ),
                                    'Request.id <>' => $query["Request"]["id"]
                                )
                            ));

                            $this->set('previous', $previous);
                            $this->set('overlap', $overlap);

                        } else {
                            $this->Session->setFlash('Deze request is al voorbij');
                            $this->redirect('/');
                        }
                    } else {
                        $this->Session->setFlash('Er is een fout in de database. Er is geen timestamp bij de authorisatie van dit verlof. <br /> RequestId:' . $query["Request"]["id"] .', AuthId:' . $query["AuthItem"]["id"]);
                        $this->redirect('/');
                    }
                } else {
                    $this->Session->setFlash('Deze request is al goedgekeurd.');
                    $this->redirect('/');
                }
            } else {
                $this->Session->setFlash('U hebt geen rechten om deze pagina te bekijken');
                $this->redirect('/');
            }
        } else {
            $this->Session->setFlash('Dit is geen geldig request');
            $this->redirect('/');
        }
    }

    public function deny($id = null) {
        if($id !== null){
            $request = $this->Request->findById($id);
            if(!empty($request)){
                $this->authorize($id, 'deny');
                $this->updateRequest($request);
                $this->Session->setFlash('Deze aanvraag is geweigerd');
                $this->redirect('/');
            }
        } else {
            $this->Session->setFlash('Dit is een ongeldig request.');
            $this->redirect('/');
        }
    }

    public function allow($id){
        if($id !== null){
            $request = $this->Request->findById($id);
            if(!empty($request)){
                $this->authorize($id, 'allow');
                $this->updateRequest($request, 'allow');
                var_dump($this->createICS('10', $request["Request"]["start_date"], $request["Request"]["end_date"], $request["CalendarItemType"]["name"], 'http://afwezig.westtoer.be/', "Verlof"));
                //$this->Session->setFlash('Deze aanvraag is goedgekeurd');
                //$this->redirect('/');
            }
        } else {
            $this->Session->setFlash('Dit is een ongeldig request.');
            $this->redirect('/');
        }
    }

    public function add(){
        $employeeId = $this->Session->read('Auth.Employee.id');

        //Fill the view information
        $this->set('requests', $this->Request->find('all', array('conditions' => array(
            'Request.employee_id' => $employeeId,
            'Request.start_date >= ' => date('Y-m-d')
        ), 'order' => 'Request.timestamp DESC')));
        $this->set('employees', $this->Employee->find('all', array('conditions' => array(
            'Employee.linked' => 1,
        ))));
        $this->set('types', $this->CalendarItemType->find('all', array('conditions' => array('CalendarItemType.user_allowed' => 1))));

        //If the request is sent
        if(($this->request->is('post'))){
            $request = $this->request->data;

            //Validation
            $validation = $this->insertValidation($request);


            if($validation == ''){
                // If no errors are found

                $requester = $this->Employee->findById($employeeId);
                $request["Request"]["employee_id"] = $employeeId;
                $request["Request"]["timestamp"] = date('Y-m-d H:i:s');

                //Create the request (with an empty AuthItem
                $this->Request->create();
                $savedRequest = $this->Request->save($request);

                if(!empty($savedRequest)){
                    $this->AuthItem->create();
                    $AuthItem = array("request_id" => $savedRequest["Request"]["id"], "supervisor_id" => $requester["Employee"]["supervisor_id"], "authorized" => 0, "authorization_date" => null);
                    $savedAuthItem = $this->AuthItem->save($AuthItem);
                    if(!empty($savedAuthItem)){
                        //Update the request with the auth item id
                        $request = $this->Request->findById($savedRequest["Request"]["id"]);
                        $request["Request"]["auth_item_id"] = $savedAuthItem["AuthItem"]["id"];
                        $savedRequest = $this->Request->save($request);
                        if(!$savedRequest["Request"]["auth_item_id"] == 0){
                            //Range
                            $range = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);
                            
                            $rangeDB = $this->CalendarDay->find('all',
                                array('conditions' => array(
                                    'CalendarDay.day_date >=' => $request["Request"]["start_date"],
                                    'CalendarDay.day_date <=' => $request["Request"]["end_date"],
                                    'CalendarDay.employee_id' => $this->Session->read('Auth.Employee.id')
                                ), 'order' => 'CalendarDay.day_date DESC')
                            );

                            if(!empty($range)){
                                foreach($rangeDB as $calendarDayRecord){
                                    $calendarDayRecords[$calendarDayRecord["CalendarDay"]["day_date"] . '/' . $calendarDayRecord["CalendarDay"]["day_time"]] = $calendarDayRecord["CalendarDay"];
                                }

                                foreach($range as $calendarDay){
                                    //Format the date
                                    $date = explode('/', $calendarDay);
                                    if(!empty($calendarDayRecords)){
                                        if(array_key_exists($calendarDay, $calendarDayRecords)){
                                            //Update


                                            //Create the request to calendar day
                                            $this->RequestToCalendarDay->create();
                                            $existingCalendarDay["CalendarDay"] = $calendarDayRecords[$calendarDay];
                                            $requestToCalendarDay = array('request_id' => $savedRequest["Request"]["id"], 'employee_id' => $request["Request"]["employee_id"], 'calendar_day_id' => $existingCalendarDay["CalendarDay"]["id"], 'auth_item_id' => $savedAuthItem["AuthItem"]["id"]);
                                            $savedRequestToCalendarDay = $this->RequestToCalendarDay->save($requestToCalendarDay);
                                            $insertCalendarDay = array(
                                                'id' => $existingCalendarDay["CalendarDay"]["id"],
                                                'day_date' => $date[0], // Could be removed
                                                'day_time' => $date[1], // Could be removed
                                                'calendar_item_type_id' => $existingCalendarDay["CalendarDay"]["calendar_item_type_id"],
                                                'replacement_id' => $existingCalendarDay["CalendarDay"]["replacement_id"], // Could be removed
                                                'request_to_calendar_days_id' => $savedRequestToCalendarDay["RequestToCalendarDay"]["id"], // Could be removed in Model
                                                'auth_item_id' => $savedAuthItem["AuthItem"]["id"]
                                            );
                                            $this->CalendarDay->save($insertCalendarDay);
                                        } else {
                                            //Create the calendar day
                                            $calendarDay = array("CalendarDay" => array(
                                                'employee_id' => $this->Session->read('Auth.Employee.id'),
                                                'replacement_id' => $request["Request"]["replacement_id"],
                                                'calendar_item_type_id' => 0,
                                                'day_date' => $date[0], 'day_time' => $date[1],
                                                'request_to_calendar_days_id' => 1,
                                                'auth_item_id' => $savedAuthItem["AuthItem"]["id"]

                                            ));
                                            $savedCalendarDay = $this->CalendarDay->save($calendarDay);
                                            $savedCalendarDay["CalendarDay"]["id"] = $this->CalendarDay->getLastInsertID();
                                            //Create the request to calendar day
                                            $this->RequestToCalendarDay->create();
                                            $requestToCalendarDay = array('request_id' => $savedRequest["Request"]["id"], 'employee_id' => $request["Request"]["employee_id"], 'calendar_day_id' => $savedCalendarDay["CalendarDay"]["id"], 'auth_item_id' => $savedAuthItem["AuthItem"]["id"]);
                                            $savedRequestToCalendarDay = $this->RequestToCalendarDay->save($requestToCalendarDay);
                                            $savedCalendarDay["CalendarDay"]["request_to_calendar_days_id"] = $savedRequestToCalendarDay["RequestToCalendarDay"]["id"];
                                            $savedCalendarDay = $this->CalendarDay->save($savedCalendarDay);
                                            unset($savedCalendarDay);
                                        }
                                    } else {
                                        //Create the calendar day
                                        $this->CalendarDay->create();
                                        $calendarDay = array("CalendarDay" => array(
                                            'employee_id' => $this->Session->read('Auth.Employee.id'),
                                            'replacement_id' => $request["Request"]["replacement_id"],
                                            'calendar_item_type_id' => 0,
                                            'day_date' => $date[0], 'day_time' => $date[1],
                                            'request_to_calendar_days_id' => 1,
                                            'auth_item_id' => $savedAuthItem["AuthItem"]["id"]

                                        ));
                                        $savedCalendarDay = $this->CalendarDay->save($calendarDay);

                                        //Create the request to calendar day
                                        $this->RequestToCalendarDay->create();
                                        $requestToCalendarDay = array('request_id' => $savedRequest["Request"]["id"], 'employee_id' => $request["Request"]["employee_id"], 'calendar_day_id' => $savedCalendarDay["CalendarDay"]["id"], 'auth_item_id' => $savedAuthItem["AuthItem"]["id"]);
                                        $savedRequestToCalendarDay = $this->RequestToCalendarDay->save($requestToCalendarDay);
                                        $savedCalendarDay["CalendarDay"]["request_to_calendar_days_id"] = $savedRequestToCalendarDay["RequestToCalendarDay"]["id"];
                                        $savedCalendarDay = $this->CalendarDay->save($savedCalendarDay);
                                        unset($savedCalendarDay);
                                    }
                                }
                            }
                        } else {
                            $this->Request->delete($savedRequest["Request"]["id"]);
                            $this->AuthItem->delete($savedAuthItem["AuthItem"]["id"]);
                        }
                    } else {
                        $this->Request->delete($savedRequest["Request"]["id"]);
                    }
                }
            //End
                $this->Session->setFlash('Uw aanvraag is ingedient. Uw aanvraag moet enkel nog goedgekeurd worden.');
                //$this->redirect('/');
            } else {
                $this->Session->setFlash($validation);
                $this->redirect(array('controller' => 'requests', 'action' => 'add'));
            }
        }
    }

    private function authorize($id, $type){
            $access = $this->Session->read('Auth.Role');
            $authoriser = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
            $request = $this->Request->findById($id);
            $authorisation = $request["AuthItem"];
            if($access["allow"] == true){
                if($authoriser["Role"]["name"] !== 'admin'){
                    if($request["Employee"]["supervisor_id"] == $authoriser["Employee"]["id"]){
                    } else {
                        $this->setFlash('U kunt geen verlof goedkeuren voor deze gebruiker');
                        return $this->redirect('/');
                    }
                }
                if($type == "allow"){
                    $authorisation["authorized"] = 1;
                    $notification = "Dit verlof is succesvol goedgekeurd";
                } else {
                    $notification = "Dit verlof is succesvol geweigerd";
                }
                $authorisation["authorization_date"] = date('Y-m-d H:i:s');

                $authorisation["supervisor_id"] = $authoriser["Employee"]["id"];
                $this->AuthItem->save($authorisation);
                $this->Session->setFlash($notification);
                //$this->redirect('/');
            } else {
                $this->Session->setFlash('U hebt geen toegang tot deze pagina.');
                return $this->redirect('/');
            }
    }

    private function updateRequest($request, $status = "deny"){
        $x = '';
        $requestToCalendarDays = $this->RequestToCalendarDay->find('all', array(
            'conditions' => array(
                'RequestToCalendarDay.request_id' => $request["Request"]["id"],
            )
        ));

        foreach($requestToCalendarDays as $requestToCalendarDay){
            $calendarDaysQuery[] = array('CalendarDay.id' => $requestToCalendarDay["RequestToCalendarDay"]["calendar_day_id"]);
        }
        if(!empty($calendarDaysQuery)){
            $calendarDays = $this->CalendarDay->find('all', array('conditions' => array('OR' => $calendarDaysQuery)));
            foreach($calendarDays as $calendarDay){
                $data[$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay;
                $currentCalendarItemType = $data[$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]]["CalendarDay"]["calendar_item_type_id"];
                    if($request["Request"]["calendar_item_type_id"] == 9){
                        $execute = true;
                    } elseif($currentCalendarItemType == 0) {
                        $execute = true;
                    } elseif($currentCalendarItemType == 9){
                        $execute = true;
                    } else {
                        $execute = false;
                    }

                    if($execute){
                        if($this->Session->read('Auth.Role.allow') == true){
                            if($this->Session->read('Auth.Role.adminpanel') == true){
                                $data[$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]]["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
                                $x = $this->CalendarDay->saveMany($data);
                            } else {
                            }
                        }
                    }
            }
        }
        return $x;
    }

    private function dateRange( $first, $last, $starttime = 'AM', $endtime = 'PM', $step = '+1 day', $format = 'Y-m-d' ){
            $dates = array();
            $current = strtotime( $first );
            $last = strtotime( $last );
            $datestime = array();

            if(strtotime($first) == $last AND $starttime == $endtime){
                $datestime[] = $first . '/' . $starttime;
            } else {
                if($starttime !== 'PM'){
                    $datestime[] = $first . '/AM';
                }

                $datestime[] = $first . '/PM';



                while( $current <= $last ) {
                    if(date('D', $current) == 'Sat' or date('D', $current) == 'Sun'){
                        $current = strtotime( $step, $current );
                    } else {
                        $dates[] = date( $format, $current );
                        $current = strtotime( $step, $current );
                    }

                }

                foreach($dates as $date){
                    if($date == $first){
                        if($starttime == 'PM'){
                            $datestime[] = $date . '/PM';

                        }
                    } elseif(strtotime($date) == $last){
                        if($endtime == 'AM'){
                            $datestime[] = $date . '/AM';
                        }
                    } else{
                        $datestime[] = $date . '/AM';
                        $datestime[] = $date . '/PM';
                    }
                }

                $datestime[] = date('Y-m-d', $last) . '/AM';
                if($endtime !== 'AM'){
                    $datestime[] = date('Y-m-d', $last) . '/PM';
                }

                if($datestime[0] == $datestime[2]){
                    unset($datestime[2]);
                    unset($datestime[3]);
                }
            }
            return $datestime;
    }

    private function insertValidation($request){
        //Validation
        $nulldate = '1970-01-01';
            $error = '';
            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d') or date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d')){
                $error .= 'U kunt niet retroactief verlof inplannen. <br />';
            }

            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) == date('Y-m-d', strtotime($nulldate)) or date('Y-m-d', strtotime($request["Request"]["end_date"])) == date('Y-m-d', strtotime($nulldate))){
                $error .= 'U hebt één of beide datums verkeerd ingegeven <br />';
            }

            if($request["Request"]["replacement_id"] == $this->Session->read("Auth.Employee.id")){
                $error .= 'Je kunt jezelf niet als vervanger opgeven. <br />';
            }

            if($request["Request"]["calendar_item_type_id"] == 0){
                $error .= "Je hebt geen reden van afwezigheid aangeduidt. <br />";
            }
            if(date('D', strtotime($request["Request"]["start_date"])) == 'Sat' or date('D', strtotime($request["Request"]["start_date"])) == 'Sun'){
                $error .= "Je afwezigheid kan niet beginnen in het weekend";
            }
            if(date('D', strtotime($request["Request"]["end_date"])) == 'Sat' or date('D', strtotime($request["Request"]["end_date"])) == 'Sun'){
                $error .= "Je afwezigheid kan niet eindigen in het weekend";
            }
            if($error !== ''){
                $error = '<strong>Fout</strong><br />' . $error;
            }
        return $error;
    }
}