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
        $this->set('title_for_layout', 'Westtoer Afwezig - Aanvragen');
    }

    //page functions
    public function index() {
        $supervisor = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
        $conditions = array('AuthItem.authorized' => false, 'AuthItem.authorization_date' => null, 'Employee.supervisor_id' => $supervisor["Employee"]["internal_id"]);
        $this->set('requests', $this->RequestToCalendarDay->Request->find('all', array('conditions' => $conditions, 'order' => 'Request.timestamp ASC')));
    }

    public function view($id = null) {
        if($id !== null){
            $query = $this->Request->findById($id);
            $requestor = $this->Employee->findById($query["Employee"]["id"]);
            $author = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
            $access = false;

            //Check if users is allowed to view this page
            if($this->Session->read('Auth.Role.allow') == true){
                $access = true;
            } elseif($requestor["Employee"]["supervisor_id"] == $author["Employee"]["internal_id"]){
                $access = true;
            }

            if($access == true){

                if($query["AuthItem"]["authorization_date"] == null){
                    if($query["AuthItem"]["authorized"] !== true){
                        if($query["Request"]["start_date"] >= date('Y-m-d')){
                            $this->set('query', $query);

                            $this->set('queryRange', $this->dateRange($query["Request"]["start_date"], $query["Request"]["end_date"], $query["Request"]["start_time"], $query["Request"]["end_time"]));
                            $previous = $this->Request->find('all', array(
                                'conditions' => array(
                                    'AuthItem.authorized' => 1,
                                    'Employee.id' => $query["Employee"]["id"]
                                ), 'limit' => 5,
                            ));
                            $overlap = $this->CalendarDay->find('all', array(
                                'conditions' => array(
                                        'day_date >= ' => date('Y-m-d', strtotime($query["Request"]["start_date"] . ' - 1 Day')),
                                        'day_date <= ' => date('Y-m-d', strtotime($query["Request"]["end_date"] . ' + 1 Day'))
                                ), 'order' => 'day_date ASC'
                            ));

                            //Find all requests that overlap that aren't authorised yet.
                            $overlapRequests = $this->Request->find('all', array(
                                'conditions' => array(
                                    'AuthItem.authorised' => 0,
                                    array('OR' =>
                                        array('start_date >= ' => date('Y-m-d', strtotime($query["Request"]["start_date"] . ' - 1 Day'))),
                                        array('end_date <= ' => date('Y-m-d', strtotime($query["Request"]["end_date"] . ' + 1 Day')))
                                        )
                                    )
                                )
                            );


                            $this->set('previous', $previous);
                            $this->set('overlapRequests', $overlapRequests);
                            foreach($overlap as $key => $overlapitem){
                                $oo[$overlapitem["CalendarDay"]["day_time"]][$overlapitem["CalendarDay"]["day_date"]][$overlapitem["Employee"]["name"] . ' ' . $overlapitem["Employee"]["surname"]] = $overlapitem;
                            }
                            if(isset($oo)){
                                $this->set('overlap', $oo);
                            }
                        } else {
                            $this->Session->setFlash('Deze request is al voorbij', 'default', array('class' => 'alert-danger'));
                            $this->redirect('/');
                        }
                    } else {
                        $this->Session->setFlash('Er is een fout in de database. Er is geen timestamp bij de authorisatie van dit verlof. <br /> RequestId:' . $query["Request"]["id"] .', AuthId:' . $query["AuthItem"]["id"], 'default', array('class' => 'alert-danger'));
                        $this->redirect('/');
                    }
                } else {
                    $this->Session->setFlash('Deze request is al goedgekeurd.', 'default', array('class' => 'alert-danger'));
                    $this->redirect('/');
                }
            } else {
                $this->Session->setFlash('U hebt geen rechten om deze pagina te bekijken', 'default', array('class' => 'alert-danger'));
                $this->redirect('/');
            }
        } else {
            $this->Session->setFlash('Dit is geen geldig request', 'default', array('class' => 'alert-danger'));
            $this->redirect('/');
        }
    }

    public function deny($id = null) {
        if($id !== null){
            $request = $this->Request->findById($id);
            if(!empty($request)){
                $this->authorize($id, 'deny');
                $this->Session->setFlash('Deze aanvraag is geweigerd');
                $this->redirect('/Request');
            }
        } else {
            $this->Session->setFlash('Dit is een ongeldig request.', 'default', array('class' => 'alert-danger'));
            $this->redirect('/Request');
        }
    }

    public function allow($id){
        if($id !== null){
            $request = $this->Request->findById($id);
            if(!empty($request)){
                $this->authorize($id, 'allow');
                $this->Session->setFlash('Deze aanvraag is goedgekeurd');
                $this->redirect('/Requests');
            }
        } else {
            $this->Session->setFlash('Dit is een ongeldig request.', 'default', array('class' => 'alert-danger'));
            $this->redirect('/Requests');
        }
    }

    public function add(){
        $employeeId = $this->Session->read('Auth.Employee.id');
        $employee = $this->Employee->findById($employeeId);

        //Fill the view information
        $this->set('requests', $this->Request->find('all', array('conditions' => array(
            'Request.employee_id' => $employeeId,
            'Request.start_date >= ' => date('Y-m-d')
        ), 'order' => 'Request.timestamp DESC')));

        $this->set('employees', $this->Employee->find('all', array('conditions' => array(
            'Employee.internal_id <>' => '-1',
            'Employee.status' => 1
        ))));
        $this->set('types', $this->CalendarItemType->find('all', array('conditions' => array('CalendarItemType.user_allowed' => 1))));

        //If the request is sent
        if(($this->request->is('post'))){
            $request = $this->request->data;
            $request = $this->addRequest($request);
            if(!empty($request)){
                $this->Session->setFlash('Je aanvraag is succesvol ingediend');
                $this->redirect($this->here, 302, true);
            }
            $this->redirect('/');
        }
    }

    public function addRequest($request){
        $this->autoRender = false;
        if(isset($request)){
            //Validation
            $validation = $this->insertValidation($request);

            if($validation == ''){
                //Create a compliant request by adding a timestamp, employee_id
                $cr = $this->completeRequest($request);

                //Create the request in the database
                $this->Request->create();
                $cr = $this->Request->save($cr);

                if(!empty($cr)){

                    //Allow us to access the users data
                    $cr["Employee"] = $this->Employee->findById($cr["Request"]["employee_id"])["Employee"];

                    //Get all the workdays that are inside the requested range by the employee
                    $requestRange = $this->dateRange($cr["Request"]["start_date"], $cr["Request"]["end_date"], $cr["Request"]["start_time"], $cr["Request"]["end_time"]);

                    //Get all the records in the database that could overlap with the range requested by the employee
                    $existingInRange = $this->CalendarDay->find('all', array('conditions' => array(
                        'CalendarDay.day_date >=' => (string)$cr["Request"]["start_date"],
                        'CalendarDay.day_date <=' => (string)$cr["Request"]["end_date"],
                        'CalendarDay.employee_id' => $cr["Request"]["employee_id"]
                    )));

                    //Convert the array to an associative array
                    if(!empty($existingInRange)){
                        $hashInRange = $this->convertToHashTable($existingInRange);
                    } else {
                        $hashInRange = array();
                    }

                    //Check where the CalendarDays overlap with the requested range
                    foreach($requestRange as $requestDate){
                        if(array_key_exists($requestDate, $hashInRange)){
                            //The CalendarDay already exists
                            $exists[] = $hashInRange[$requestDate];
                        } else {
                            //The CalendarDay doesn't exist
                            $notexist[] = $requestDate;
                        }
                    }

                    //Create an Auth Item to authenticate against
                    $this->AuthItem->create();
                    $preAuthItem = array('request_id' => $cr["Request"]["id"], 'supervisor_id' => $cr["Employee"]["supervisor_id"], 'authorized' => 0);
                    $ai = $this->AuthItem->save($preAuthItem);

                    //Update the request with the new AuthItem
                    $request = $this->Request->findById($cr["Request"]["id"]);
                    $request["Request"]["auth_item_id"] = $ai["AuthItem"]["id"];
                    $cr = $this->Request->save($request);


                    //All the dates that don't exist may be created
                    if(!empty($notexist)){
                        foreach($notexist as $ne){

                            //Creating an Request To Calendar Days-record
                            $this->RequestToCalendarDay->create();
                            $rtcd = array(
                                'RequestToCalendarDay' => array(
                                    'request_id' => $cr["Request"]["id"],
                                    'calendar_day_id' => $ne . '/' . $cr["Employee"]["id"],
                                    'auth_item_id' => $cr["Employee"]["id"]
                                )
                            );
                            $rtcd = $this->RequestToCalendarDay->save($rtcd);

                            //Add the calendar days to one array to later be added to the db
                            $cd[] = array('CalendarDay' =>
                                array(
                                    'employee_id' => $cr["Request"]["employee_id"],
                                    'day_date' => explode('/', $ne)[0],
                                    'day_time' => explode('/', $ne)[1],
                                    'calendar_item_type_id' => 9,
                                    'replacement_id' => $cr["Request"]["replacement_id"],
                                    'request_to_calendar_days_id' => $rtcd["RequestToCalendarDay"]["id"],
                                    'auth_item_id' => $ai["AuthItem"]["id"]
                                )
                            );

                        }
                    }

                    if(!empty($exists)){
                        foreach($exists as $e){

                            //Creating an Request To Calendar Days-record
                            $this->RequestToCalendarDay->create();
                            $rtcd = array(
                                'RequestToCalendarDay' => array(
                                    'request_id' => $cr["Request"]["id"],
                                    'calendar_day_id' => $e["CalendarDay"]["day_date"] . '/' . $e["CalendarDay"]["day_time"] . '/' . $cr["Employee"]["id"],
                                    'auth_item_id' => $cr["Employee"]["id"]
                                )
                            );
                            $rtcd = $this->RequestToCalendarDay->save($rtcd);

                            //Add the calendar days to one array to later be added to the db
                            $cd[] = array('CalendarDay' =>
                                array(
                                    'id' => $e["CalendarDay"]["id"],
                                    'employee_id' => $cr["Request"]["employee_id"],
                                    'day_date' => $e["CalendarDay"]["day_date"],
                                    'day_time' => $e["CalendarDay"]["day_time"],
                                    'calendar_item_type_id' => $e["CalendarDay"]["calendar_item_type_id"],
                                    'replacement_id' => $cr["Request"]["replacement_id"],
                                    'request_to_calendar_days_id' => $rtcd["RequestToCalendarDay"]["id"],
                                    'auth_item_id' => $ai["AuthItem"]["id"]
                                )
                            );
                        }
                    }
                    $this->Session->setFlash('Aanvraag succesvol opgeslagen.');
                    $supervisor = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $cr["Employee"]["supervisor_id"], "Employee.internal_id <>" => '-1')));
                    $body = $cr["Employee"]["surname"] . ' ' . $cr["Employee"]["name"] . ' heeft een nieuwe aanvraag ingediend. Om deze te bekijken, ga je naar ' . Configure::read('Administrator.base_fallback_url') . '/users/login?router=' . Configure::read('Administrator.base_fallback_url') . '/Requests/view/' . $cr["Request"]["id"];
                    if(empty($cd)){
                        $this->sendMailToHR("new", $cr);
                        if(!empty($supervisor)){
                            if(isset($supervisor["Employee"]["3gram"])){
                                $this->sendMail($this->trigramToMail($supervisor["Employee"]["3gram"]), $body, 'Nieuwe aanvraag');
                            }
                        }
                    } else {
                        if($this->CalendarDay->saveMany($cd)){
                            $this->sendMailToHR("new", $cr);
                            if(!empty($supervisor)){
                                if(isset($supervisor["Employee"]["3gram"])){
                                    $this->sendMail($this->trigramToMail($supervisor["Employee"]["3gram"]), $body, 'Nieuwe aanvraag');
                                }
                            }
                        } else {
                            $this->Session->setFlash('Aanvraag kon niet worden opgeslagen.', 'default', array('class' => 'alert-danger'));
                        }
                    }

                    return $cr;
                }
            } else {
                $this->Session->setFlash($validation, 'default', array('class' => 'alert-danger'));
                $this->redirect($this->here);
            }
        } else {
            $this->redirect('/');
        }
    }

    public function authorize($id, $type){
        $this->autoRender = false;
        if(isset($id) and isset($type)){
            $author = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
            $request = $this->Request->findById($id);
            $requestor = $this->Employee->findById($request["Employee"]["id"]);
            $access = false;

            $genericBody = 'Je aanvraag voor ' . $request["CalendarItemType"]["name"] . ' vanaf '
                . $request["Request"]["start_date"] . '-' . $request["Request"]["start_time"] .
                ' tot ' . $request["Request"]["end_date"] . '-' . $request["Request"]["end_time"] . ' ';

            if($author["Role"]["allow"] == true){
                $access = true;
            } elseif($requestor["Employee"]["supervisor_id"] == $author["Employee"]["internal_id"]){
                $access = true;
            }

            if($access == true){

                if($author["Role"]["name"] !== "admin"){
                    if($request["Employee"]["supervisor_id"] !== $author["Employee"]["internal_id"]){
                        $this->Session->setFlash('Voor deze gebruiker kan je geen verlof goedkeuren.', 'default', array('class' => 'alert-danger'));
                    }
                }

                if($type == 'allow'){
                    if($this->AuthItemUpdate($request)){
                        if(isset($request["Employee"]["3gram"])){
                            $this->sendMail($this->trigramToMail($request["Employee"]["3gram"]), $genericBody . 'is goedgekeurd', 'Je afwezigheid is goedgekeurd');
                        }
                        $this->sendMailToHR('allowed', $request);
                    }
                } else {
                    if(isset($request["Employee"]["3gram"])){
                        $this->sendMail($this->trigramToMail($request["Employee"]["3gram"]), $genericBody . 'is geweigerd', 'Je afwezigheid is geweigerd');
                    }
                    $this->sendMailToHR('denied', $request);
                }

            } else {
                $this->redirect('/');
            }
        } else {
            $this->redirect('/');
        }

    }

    private function AuthItemUpdate($request){
        $authorization["AuthItem"] = $request["AuthItem"];
        $authorization["AuthItem"]["authorized"] = 1;
        $authorization["AuthItem"]["authorization_date"] = date('Y-m-d H:i:s');
        $range = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);
        $employee["Employee"] = $request["Employee"];
        $cp = array();
        $gateKeeper = false;

        //Find all calendar days between range
        foreach($range as $date){
            $conditions[] = array('employee_id' => $employee["Employee"]["id"], 'day_date' => explode('/', $date)[0], 'day_time' => explode('/', $date)[1]);
        }

        $calendarDays = $this->CalendarDay->find('all', array('conditions' => array('OR' => $conditions)));

        //Check if the users balance is applicable and if so, if it is high enough
        if($request["Request"]["calendar_item_type_id"] == 23){
            $prevCost = $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.calendar_item_type_id' => 23), 'CalendarDay.employee_id' => $request["Employee"]["id"]));
            $balance = $employee["Employee"]["daysleft"] - $prevCost;
            if($balance > 0 ){
                $gateKeeper = true;
            } else {
                $this->Session->setFlash('Deze gebruiker heeft onvoldoende dagen over om dit goed te keuren.', 'default', array('class' => 'alert-danger'));
                return false;
            }
        } else {
            $gateKeeper = true;
        }

        //Save the AuthItem and check if we may overwrite the days
        if($this->AuthItem->save($authorization)){
            if($request["Request"]["calendar_item_type_id"] <> 9){
                foreach($calendarDays as $key => $calendarDay){
                    if($calendarDay["CalendarDay"]["calendar_item_type_id"] == 9){
                        $cp[$key] = $calendarDay;
                        $cp[$key]["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
                    }
                }
            } else {
                foreach($calendarDays as $key => $calendarDay){
                        $cp[$key] = $calendarDay;
                        $cp[$key]["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
                }
            }
        }

        //Execute the save
        if($gateKeeper == true){
            if($this->CalendarDay->saveMany($cp)){
                $this->Session->setFlash('De aanvraag is goedgekeurd.');
            }
        }

        return true;

    }

    private function dateRange( $first, $last, $starttime = 'AM', $endtime = 'PM', $step = '+1 day', $format = 'Y-m-d' ){
            $dates = array();
            $current = strtotime( $first );
            $last = strtotime( $last );
            $first = strtotime( $first );
            $datestime = array();

            if(date('Y-m-d', $first) == date('Y-m-d', $last) AND $starttime == $endtime){
                $datestime[] = $first . '/' . $starttime;
            } else {

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
                        } else {
                            $datestime[] = $date . '/AM';
                            $datestime[] = $date . '/PM';
                        }
                    } elseif(strtotime($date) == $last){
                        if($endtime == 'AM'){
                            $datestime[] = $date . '/AM';
                        } else {
                            $datestime[] = $date . '/AM';
                            $datestime[] = $date . '/PM';
                        }
                    } else{
                        $datestime[] = $date . '/AM';
                        $datestime[] = $date . '/PM';
                    }
                }
            }
            return $datestime;
    }

    private function insertValidation($request){
        //Validation
        $nulldate = '1970-01-01';
        if(empty($request["Employee"])){
            $request["Employee"]["id"] = $this->Session->read('Auth.Employee.id');
        }
        $request["Employee"] = $this->Employee->findById($request["Employee"]["id"])["Employee"];
        $dateRange = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);
            $error = '';
            /*
             * Issue #33
             *
             * if(date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d') or date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d')){
             *   $error .= 'U kunt niet retroactief verlof inplannen. <br />';
             * }
             *
            */

            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) > date('Y-m-d', strtotime($request["Request"]["end_date"]))){
                $error .= 'De einddatum kan niet voor de begindatum komen. <br />';
            } elseif(date('Y-m-d', strtotime($request["Request"]["start_date"])) == date('Y-m-d', strtotime($request["Request"]["end_date"]))){
                if($request["Request"]["start_time"] == 'PM' and $request["Request"]["end_time"] == "AM"){
                    $error .= 'De einddatum kan niet voor de begindatum komen. <br />';
                }
            }

            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) == date('Y-m-d', strtotime($nulldate)) or date('Y-m-d', strtotime($request["Request"]["end_date"])) == date('Y-m-d', strtotime($nulldate))){
                $error .= 'U hebt één of beide datums verkeerd ingegeven <br />';
            }

            if($request["Request"]["replacement_id"] == $request["Employee"]["id"]){
                $error .= 'Je kunt jezelf niet als vervanger opgeven. <br />';
            }

            if($request["Request"]["calendar_item_type_id"] == 0){
                $error .= "Je hebt geen reden van afwezigheid aangeduidt. <br />";
            }
            if(date('D', strtotime($request["Request"]["start_date"])) == 'Sat' or date('D', strtotime($request["Request"]["start_date"])) == 'Sun'){
                $error .= "Je afwezigheid kan niet beginnen in het weekend <br />";
            }

            if(date('D', strtotime($request["Request"]["end_date"])) == 'Sat' or date('D', strtotime($request["Request"]["end_date"])) == 'Sun'){
                $error .= "Je afwezigheid kan niet eindigen in het weekend <br />";
            }

            if($request["Request"]["calendar_item_type_id"] == 23){
                $prevCost = $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.calendar_item_type_id' => 23, 'CalendarDay.employee_id' => $request["Employee"]["id"])));
                $balance = $request["Employee"]["daysleft"] - $prevCost;
                $price = count($this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]));

                //Calculate how many days are already vacation
                $discount = array(
                    'start' => $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.day_date >=' => $request["Request"]["start_date"],'CalendarDay.calendar_item_type_id' => 23, 'CalendarDay.employee_id' => $request["Employee"]["id"]))),
                    'between' => $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.day_date >=' => date('Y-m-d', strtotime($request["Request"]["start_date"] . ' + 1 Day')), 'CalendarDay.day_date <=' => date('Y-m-d', strtotime($request["Request"]["end_date"] . ' - 1 Day')),'CalendarDay.calendar_item_type_id' => 23, 'CalendarDay.employee_id' => $request["Employee"]["id"]))),
                    'end' => $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.day_date >=' => $request["Request"]["end_date"],'CalendarDay.calendar_item_type_id' => 23, 'CalendarDay.employee_id' => $request["Employee"]["id"])))

                );
                if($discount["start"] == 2){
                    if($request["Request"]["start_time"] == 'PM'){
                        $discount["start"]--;
                    }
                }

                if($discount["end"] == 2){
                    if($request["Request"]["end_time"] == "AM"){
                        $discount["end"]--;
                    }
                }


                $discount = array_sum($discount);
                $price = $price - $discount;

                if($price > $balance){
                    $error .= "Je hebt niet genoeg vakantiedagen over. <br />";
                }

            }

            $supervisor = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $request["Employee"]["supervisor_id"], 'Employee.internal_id <>' => '-1')));
            if(empty($supervisor)){
                $error .= "Je hebt nog geen verantwoordelijke. Contacteer HR en vraag om dit te corrigeren. <br />";
            }
        return $error;
    }

    private function sendMail($receiver, $body, $subject = "Westtoer afwezig"){
        $Email = new CakeEmail('westtoer');
        $Email->to($receiver);
        $Email->subject($subject);
        $Email->replyTo('noreply@westtoer.be');
        $Email->from ('noreply@westtoer.be');
        $Email->send($body);
    }

    private function trigramToMail($trigram){
        if(strpos($trigram, '@')){
            return $trigram;
        } else {
            return $trigram . '@westtoer.be';
        }
    }

    private function completeRequest($request){
        $compliantRequest = $request;
        if(isset($request["Request"]["origin"])){
            if($request["Request"]["origin"] == 'AdminPanel'){
                if($this->Session->read('Auth.Role.adminpanel') == true){
                    $compliantRequest["Request"]["employee_id"] = $request["Request"]["employee_id"];
                }
            }
        } else {
            $compliantRequest["Request"]["employee_id"] = $this->Session->read('Auth.Employee.id');
        }

        $compliantRequest["Request"]["timestamp"] = date('Y-m-d H:i:s');
        $compliantRequest["Request"]["timestamp"] = date('Y-m-d H:i:s');

        return $compliantRequest;
    }

    private function convertToHashTable($unhashed){
        foreach($unhashed as $u){
            $hashed[$u["CalendarDay"]["day_date"] . '/' . $u["CalendarDay"]["day_time"]] = $u;
        }

        return $hashed;
    }

    private function sendMailToHR($type = "new", $request = array()){
        $allHR = $this->Employee->find('all', array('conditions' => array('Employee.role_id' => 2)));
        foreach($allHR as $HR){
            $Email = new CakeEmail('westtoer');
            $Email->to($this->trigramToMail($HR["Employee"]["3gram"]));
            $Email->subject('Westtoer Afwezig');
            $Email->replyTo('noreply@westtoer.be');
            $Email->from ('noreply@westtoer.be');

            if($type == "new"){
                $Email->send($request["Employee"]["name"] . ' ' . $request["Employee"]["surname"] . ' heeft een nieuwe aanvraag gedaan die zou beginnen op ' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . ' en zou eindigen op ' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '. Om dit te bekijken ga je naar http://afwezig.westtoer.be/Requests/view/' . $request["Request"]["id"]);
            } elseif($type == "allowed") {
                $Email->send($request["Employee"]["name"] . ' ' . $request["Employee"]["surname"] . ' had een aanvraag gedaan voor die zou beginnen op ' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . ' en zou eindigen op ' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '. Deze aanvraag is goedgekeurd.');
            } elseif($type == "denied"){
                $Email->send($request["Employee"]["name"] . ' ' . $request["Employee"]["surname"] . ' had een aanvraag gedaan voor die zou beginnen op ' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . ' en zou eindigen op ' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '. Deze aanvraag is geweigerd.');
            }
        }
    }
}