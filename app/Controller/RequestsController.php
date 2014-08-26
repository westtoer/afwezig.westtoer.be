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
        $access = $this->Session->read('Auth.Role');
        $supervisor = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
        if($access["allow"] == true){
            if($supervisor["Role"]["name"] == 'admin'){
                $conditions = array('AuthItem.authorized' => false, 'AuthItem.authorization_date' => null, 'Request.start_date >=' => date('Y-m-d'));
            } else {
                $conditions = array('AuthItem.authorized' => false, 'AuthItem.authorization_date' => null, 'Employee.supervisor_id' => $supervisor["Employee"]["id"], 'Request.start_date >=' => date('Y-m-d'));
            }
            $this->set('requests', $this->RequestToCalendarDay->Request->find('all', array('conditions' => $conditions, 'order' => 'Request.timestamp ASC')));
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

                            $this->set('previous', $previous);
                            foreach($overlap as $key => $overlapitem){
                                $oo[$overlapitem["CalendarDay"]["day_time"]][$overlapitem["CalendarDay"]["day_date"]][$overlapitem["Employee"]["name"] . ' ' . $overlapitem["Employee"]["surname"]] = $overlapitem;
                            }
                            if(isset($oo)){
                                $this->set('overlap', $oo);
                            }
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
                $this->Session->setFlash('Deze aanvraag is goedgekeurd');
                $this->redirect('/');
            }
        } else {
            $this->Session->setFlash('Dit is een ongeldig request.');
            $this->redirect('/');
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
        ))));
        $this->set('types', $this->CalendarItemType->find('all', array('conditions' => array('CalendarItemType.user_allowed' => 1))));

        //If the request is sent
        if(($this->request->is('post'))){
            $request = $this->request->data;
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
                    $this->Request->save($request);


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

                    if(empty($cd)){
                        $this->sendMailToHR("new", $cr);
                        $this->redirect($this->here);
                    } else {
                        if($this->CalendarDay->saveMany($cd)){
                            $this->sendMailToHR("new", $cr);
                            $this->redirect($this->here);
                        }
                    }
                }
            } else {
                $this->Session->setFlash($validation);
                $this->redirect($this->here);
            }
        }
    }

    private function authorize($id, $type){
        $author = $this->Employee->findById($this->Session->read('Auth.Employee.id'));
        $request = $this->Request->findById($id);

        $genericBody = 'Je aanvraag voor ' . $request["CalendarItemType"]["name"] . ' vanaf '
            . $request["Request"]["start_date"] . '-' . $request["Request"]["start_time"] .
            ' tot ' . $request["Request"]["end_date"] . '-' . $request["Request"]["end_time"];

        if($author["Role"]["allow"] == true){

            if($author["Role"]["name"] !== "admin"){
                if($request["Employee"]["supervisor_id"] !== $author["Employee"]["internal_id"]){
                    $this->Session->setFlash('Voor deze gebruiker kan je geen verlof goedkeuren.');
                }
            }

            if($type == 'allow'){
                if($this->AuthItemUpdate($request)){
                    $this->sendMail($this->trigramToMail($request["Employee"]["3gram"]), $genericBody . 'is goedgekeurd', 'Je afwezigheid is goedgekeurd');
                    $this->sendMailToHR('allowed', $request);
                }
            } else {
                $this->sendMail($this->trigramToMail($request["Employee"]["3gram"]), $genericBody . 'is geweigerd', 'Je afwezigheid is geweigerd');
                $this->sendMailToHR('denied', $request);
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

        foreach($range as $date){
            $conditions[] = array('employee_id' => $employee["Employee"]["id"], 'day_date' => explode('/', $date)[0], 'day_time' => explode('/', $date)[1]);
        }

        $calendarDays = $this->CalendarDay->find('all', array('conditions' => array('OR' => $conditions)));

        if($request["Request"]["calendar_item_type_id"] == 23){
            foreach($calendarDays as $key => $calendarDay){
                $cp[$key] = $calendarDay;
                $cp[$key]["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
            }
        }

        $calculatedCost = $employee["Employee"]["daysleft"] - count($cp);

        if($calculatedCost >= 0){
            if($this->AuthItem->save($authorization)){
                if($request["Request"]["calendar_item_type_id"] <> 9){
                    if($request["Request"]["calendar_item_type_id"] == 23){
                        foreach($calendarDays as $key => $calendarDay){
                            if($calendarDay["CalendarDay"]["calendar_item_type_id"] == 9){
                                $cp[$key] = $calendarDay;
                                $cp[$key]["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
                            }
                        }
                    }
                }
                if($this->CalendarDay->saveMany($cp)){
                    $employee["Employee"]["daysleft"] = $calculatedCost;
                    if($this->Employee->save($employee)){
                        $this->Session->setFlash("Aanvraag is goedgekeurd.");
                        $this->redirect('/');
                    }
                }
            }
        } else {
            $this->Session->setFlash("Deze gebruiker heeft niet genoeg vakantiedagen meer.");
            $this->redirect('/');
        }
    }

    private function updateRequest($request, $status = 'deny'){
        if($status == 'allow'){
            $range = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);

            foreach($range as $date){
                $or[] = array('day_date' => explode('/',$date)[0], 'day_time' => explode('/',$date)[1]);
            }

            $calendarDays = $this->CalendarDay->find('all', array('conditions' => array('OR' => $or), array('calendar_item_type_id' => $request["Request"]["calendar_item_type_id"])));

            foreach($calendarDays as $calendarDay){
                if($calendarDay["CalendarDay"]["calendar_item_type_id"] == 9){ //Type 9 is workday
                    $execution[] = $calendarDay;
                } else {

                }
            }
        }
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
        $request["Employee"] = $this->Employee->findById($this->Session->read('Auth.Employee.id'))["Employee"];
        $dateRange = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);
            $error = '';
            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d') or date('Y-m-d', strtotime($request["Request"]["start_date"])) < date('Y-m-d')){
                $error .= 'U kunt niet retroactief verlof inplannen. <br />';
            }

            if(date('Y-m-d', strtotime($request["Request"]["start_date"])) > date('Y-m-d', strtotime($request["Request"]["end_date"]))){
                $error .= 'De einddatum kan niet voor de begindatum komen. <br />';
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
                $error .= "Je afwezigheid kan niet beginnen in het weekend <br />";
            }

            if(date('D', strtotime($request["Request"]["end_date"])) == 'Sat' or date('D', strtotime($request["Request"]["end_date"])) == 'Sun'){
                $error .= "Je afwezigheid kan niet eindigen in het weekend <br />";
            }

            if($request["Request"]["calendar_item_type_id"] == 23){
                $count = 0;
                if(($request["Employee"]["daysleft"] - count($dateRange)) < 0){
                    foreach($dateRange as $date){
                        $conditions["OR"] = array('day_date' => explode('/', $date)[0], 'day_time' => explode('/', $date)[1]);
                    }
                    foreach($this->CalendarDay->find("all", array('conditions' => $conditions)) as $calendarDay){
                        if($calendarDay["CalendarDay"]["calendar_item_type_id"] == 9){
                            $count++;
                        }
                    }

                    if($count > $request["Employee"]["daysleft"]){
                        $error .="Je hebt te weinig verlofdagen om deze aanvraag in te dienen. Om dit toch in te dienen, neem je contact op met HR.";
                    }
                }
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
        $compliantRequest["Request"]["employee_id"] = $this->Session->read('Auth.Employee.id');
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
        $allHR = $this->Employee->find('all', array('conditions' => array('Employee.role_id <' => 3)));
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