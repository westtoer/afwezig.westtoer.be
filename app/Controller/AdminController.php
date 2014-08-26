<?php
App::uses('ConnectionManager', 'Model');
App::uses('CakeEmail', 'Network/Email');
class AdminController extends AppController {

    public $uses = array('User', 'Employee', 'Request', 'EmployeeDepartment', 'RequestToCalendarDay', 'CalendarDay', 'AdminVariable', 'AuthItem', 'Stream', 'CalendarItemType');
    public $helpers = array('Employee', 'Request', 'CalendarDay', 'CalendarItemType', 'Xls');
    public $components = array('RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
        if($this->Session->read('Auth.Role.id') !== '1'){
            if($this->Session->read('Auth.Role.id') !== '2'){
                $this->Session->setFlash('Je hebt geen rechten in het administratiepaneel.');
                $this->redirect('/');
            }
        }
        $this->set('title_for_layout', 'Westtoer Afwezig - Administratie');
    }

    public function index(){
        $this->Session->write('Auth.Admin.token', rand(9,40));
    }
    //General settings
    //Admin section for Employees
    public function registerEmployee(){
        $this->set('departments', $this->EmployeeDepartment->find('all'));
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'))));
        $this->set('');
        if($this->request->is('post')){

            $this->Employee->create();
            $employee = $this->request->data;
            $existing = $this->Employee->find('first', array('conditions' => array('Employee.name' => $employee["Employee"]["name"], 'Employee.surname' => $employee["Employee"]["surname"], 'Employee.internal_id' => $employee["Employee"]["internal_id"])));
            if(empty($existing)){
                $this->Employee->save($employee);
            } else {
                $this->Session->setFlash("Er bestaat al een gebruiker met dezelfde naam en telefoonnummer");
                $this->redirect('/Admin/viewEmployees');
            }
        }
    }

    public function viewEmployees(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'), 'order' => 'Employee.name ASC')));

    }

    public function editEmployee($id = null){
        if($this->request->is('post')){
            $employee = $this->request->data;
            $this->Employee->save($employee);
            $this->redirect(array('action' => 'viewEmployees'));
        } else {
            if($id !== null){
                $this->set('employee', $this->Employee->findById($id));
                $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.name <>' => "" ))));
                $this->set('departments', $this->EmployeeDepartment->find('all'));
            } else {
                $this->Session->setFlash('Je hebt geen geldige gebruiker geselecteerd.');
            }
        }
    }

    public function viewRegistrations(){
        $this->set('registrations', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
    }

    public function viewUsers(){
        $this->set('usersActive', $this->User->find('all', array('conditions' => array('User.status' => 'active'))));
        $this->set('usersPending', $this->User->find('all', array('conditions' => array('User.status' => 'requested'))));
        $this->set('usersDenied', $this->User->find('all', array('conditions' => array('User.status' => 'denied'))));
    }

    //Admin section for Calendar Items
    public function viewPendingCalendarItems(){
       $this->set('toBeAllowed', $this->Request->find('all', array('conditions' => array(
            'AuthItem.authorized' => 0,
            'AuthItem.authorization_date' => null,
            'Request.start_date >=' => date('Y-m-d')
        ))));
    }

    public function GeneralCalendarItems(){
        $this->set('requests', $this->Request->find('all', array(
            'conditions' => array(
                'Request.employee_id' => 4,
                'Request.name <>' => '',
                'OR' => array(array('Request.calendar_item_type_id' => 3), array('Request.calendar_item_type_id' => 23))

            )
        )));
        $employees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1')));
        $authorizer = $this->Session->read('Auth.Employee.id');

        if($this->request->is('post')){
            $request = $this->request->data;
                $request["Request"]["employee_id"] = 4;
                if($request["Request"]["type"] == 0){
                    $request["Request"]["calendar_item_type_id"] = 3;
                } else {
                    $request["Request"]["calendar_item_type_id"] = 23;
                }

                $request["Request"]["timestamp"] = date('Y-m-d H:i:s');
                $request["Request"]["replacement_id"] = '-1';
                $request["Request"]["auth_item_id"] = 4;
                $this->Request->create();
                if($request["Request"]["start_date"] < date('Y-m-d', strtotime(date('Y') . '-12-31'))){
                    $savedRequest = $this->Request->save($request);
                } else {
                    $this->Session->setFlash("Je kan enkel Feestdagen voor dit jaar ingegeven");
                    $this->redirect($this->here);
                }
            $this->AuthItem->create();
            $authItem = array('request_id' => $savedRequest["Request"]["id"], 'supervisor_id' => $authorizer, 'authorization_date' => date('Y-m-d H:i:s'), 'message' => "Holiday at request " . $savedRequest["Request"]["id"]);
            $authItem = $this->AuthItem->save($authItem);
            var_dump($authItem);
            $savedRequest["Request"]["auth_item_id"] = $authItem["AuthItem"]["id"];
            $savedRequest = $this->Request->save($savedRequest);

            foreach($employees as $employee){
                $range = $this->dateRange($request["Request"]["start_date"], $request["Request"]["end_date"], $request["Request"]["start_time"], $request["Request"]["end_time"]);
                foreach($range as $date){
                    $calendarDays[] = array('CalendarDay' => array(
                        'employee_id' => $employee["Employee"]["id"],
                        'day_date' => explode('/', $date)[0],
                        'day_time' => explode('/', $date)[1],
                        'calendar_item_type_id' => $request["Request"]["calendar_item_type_id"],
                        'replacement_id' => '-1',
                        'auth_item_id' => $authItem["AuthItem"]["id"]
                    ));


                }
            }

            $this->CalendarDay->saveMany($calendarDays);
            $this->redirect($this->here);


        } elseif(isset($this->request->params["named"]["id"])){
            if(isset($this->request->params["named"]["action"])){
                if($this->request->params["named"]["action"] == 'delete'){
                    $request = $this->Request->findById($this->request->params["named"]["id"]);
                    if(!empty($request)){
                        if($this->CalendarDay->deleteAll(array('CalendarDay.auth_item_id' => $request["Request"]["auth_item_id"]))){
                            $this->Request->delete($request["Request"]["id"]);
                            $this->Session->setFlash('Deze algemene feestdag is verwijderd.');
                            $this->redirect(array('controller' => 'admin', 'action' => 'GeneralCalendarItems'));
                        }
                    }
                }
            }
        }
    }

    public function endOfYear(){
        $webroot = '/var/www/html/afwezig.westtoer.be/app/webroot';
        //if(date('m') > '11'){
            $this->layout = 'wizard';
            if(!empty($this->request->query["step"])){
                $this->set('step', $this->request->query["step"]);
                $step = $this->request->query["step"];
                if($step == '1'){
                    $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'))));
                    $this->admin_variable('lockApp', 'write', 'true');
                } elseif($step == '2') {
                    $this->updateDaysLeft($this->request->data);
                    $this->redirect('/admin/endOfYear?step=3');
                } elseif($step == '3'){
                    $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'))));
                    if($this->request->is('post')){
                        $this->updateDaysLeft($this->request->data);
                        $this->redirect('/admin/endOfYear?step=4');
                    }
                } elseif($step == '5'){
                    $referenceDate =  date('Y-m-d', strtotime('01-01-' . (date('Y')-2)));
                    $this->CalendarDay->deleteAll(array('day_date <= ' => $referenceDate));
                    $this->Request->deleteAll(array('end_date <= ' => $referenceDate));
                    $this->RequestToCalendarDay->deleteAll(array('Request.end_date <=' => $referenceDate));
                    $this->AuthItem->deleteAll(array('authorization_date <= ' => date('Y-m-d H:i:s', strtotime($referenceDate))));
                    $this->redirect('/admin/endOfYear?step=6');
                } elseif($step == '6'){
                    $this->set('holidays', $this->Request->find('all', array('conditions' => array('Request.calendar_item_type_id' => 3))));
                } elseif($step == '7'){
                    $duplicates = '';
                    $incomingHolidays = $this->request->data["Request"];
                    if(!empty($incomingHolidays)){
                        foreach($incomingHolidays as $incomingHoliday) {
                            if(isset($incomingHoliday["request_copy"])){
                                $origin = $this->Request->findById($incomingHoliday["id"]);
                                $duplicate = $origin;
                                $duplicate["Request"]["start_date"] = date('Y-m-d', strtotime($origin["Request"]["start_date"] . '+ 1 Year'));
                                $duplicate["Request"]["end_date"] = date('Y-m-d', strtotime($origin["Request"]["end_date"] . '+ 1 Year'));
                                $duplicates[] = $duplicate;
                            }
                        }
                        $this->Request->saveMany($duplicates);
                    }
                    $this->redirect('/admin/endOfYear?step=8');
                } elseif($step == '8'){
                    $this->set('streams', $this->Stream->find('all', array('group' => 'employee_id')));
                } elseif($step == '9'){
                   $incomingStreams = $this->request->data;
                    if(!empty($incomingStreams)){

                        foreach($incomingStreams as $employee => $stream){
                            if($stream == 'on'){
                                $streams[$employee][] = $this->Stream->find('all', array('conditions' => array('employee_id' => $employee)));
                            }
                        }

                        foreach($streams as $employees){
                            foreach($employees as $key =>$stream){
                                if($stream["Stream"]["calendar_item_type_id"] == 9){
                                    unset($employees[$key]);
                                }
                            }

                            foreach($employees as $stream){
                                if($stream["Stream"]["relative_nr"] > 5){
                                    $dateArray = $this->getRange(date('Y-m-d', strtotime($this->getNofYear($stream["Stream"]["day_nr"], 'first', 0) . ' + 7 Days')), $this->getNofYear($stream["Stream"]["day_nr"], 'last', 0), 'ww');
                                } else {
                                    $dateArray = $this->getRange($this->getNofYear($stream["Stream"]["day_nr"], 'first', 0), $this->getNofYear($stream["Stream"]["day_nr"], 'last', 0), 'ww');

                                }

                                $inserts[] = $this->createManyCalendarDays($dateArray, $stream["Stream"]["calendar_item_type_id"], $key, $this->Session->read('Auth.Employee.id'), $stream["Stream"]["day_time"]);

                            }

                            $finished = 0;
                            $size = count($inserts);

                            foreach($inserts as $insert){
                                if($this->CalendarDay->saveMany($insert)){
                                    $finished++;
                                }
                            }
                        }
                    }
                } elseif($step == '10'){
                    $this->layout = 'default';
                    $x = $this->createBackup();
                    $this->sendSqlAndMail($x, "There's a backup made for the completion of the End of Year procedure. It's located here: ");
                } elseif($step == '11'){
                    $this->Session->setFlash('De wizard is voltooid.');
                    $this->admin_variable('lockApp', 'write', 'false');
                    $this->redirect("/");
                }
            } else {
                $x = $this->createBackup();
                $this->sendSqlAndMail($x, "There's a backup made for the init of the End of Year procedure. It's located here: ");

            }
        //} else {
         //   $this->Session->setFlash('Voor december kun je het einde van het jaar niet boeken.');
         //   $this->redirect(array('action' => 'index'));
        //}
    }


    //Section for streams

    public function addStream(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'), 'order' => 'Employee.name ASC')));
        $this->set('calendaritemtypes', $this->CalendarItemType->find('all'));

        if($this->request->is('post')){
            $streamObjects = array();
            $incomingStream = $this->request->data;
            if(count($incomingStream["Stream"]["elements"]) < 20){
                foreach($incomingStream["Stream"]["elements"] as $key => $element){
                    $key = explode('-', $key);
                    $incomingStream["Stream"]["elements"][$key[0] . '-' . ($key[1] + 5) . '-' . $key[2]] = $element;

                }
            }

            if($incomingStream["Stream"]["employee_id"] != '-1'){
                $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $incomingStream["Stream"]["employee_id"])));
                $streams = $this->Stream->find('first', array('conditions' => array('employee.id' => $employee["Employee"]["internal_id"])));
                if(empty($stream)){
                    foreach($incomingStream["Stream"]["elements"] as $date => $element){
                        $date = explode('-', $date);
                        $streamObjects[] = array('Stream' => array(
                            'employee_id' => $incomingStream["Stream"]["employee_id"],
                            'calendar_item_type_id' => $element,
                            'relative_nr' => $date[1],
                            'day_time' => $date[2],
                            'day_nr' => date('N', strtotime($date[0]))
                        ));
                    }

                    if($this->Stream->saveMany($streamObjects)){
                        $this->Session->setFlash('Stramien opgeslagen.');
                        $this->redirect($this->here);
                    } else {
                        $this->Session->setFlash('Het stramien kon niet worden opgeslagen.');
                        $this->redirect($this->here);
                    }
                } else {
                    $this->Session->setFlash('Deze gebruiker heeft al een stramien.');
                    $this->redirect($this->here);
                }
            } else {
                $this->Session->setFlash('Je moet een geldige gebruiker opgeven.');
                $this->redirect($this->here);
            }

        }
    }

    public function viewStreams(){
        $hasStream = $this->Stream->find('list', array('fields' => array('employee_id'), 'group' => 'employee_id'));
        $this->Employee->unbindModel(array('hasMany' => array('User', 'CalendarDay'), 'belongsTo' => array('Role', 'EmployeeDepartment')));

        foreach($hasStream as $employee){
            $employees[] = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $employee)));
        }

        $this->set('employees', $employees);
    }

    public function removeStream($id = null){
        if($id != null){
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $id)));
            if(!empty($employee)){
                if($this->Stream->deleteAll(array('employee_id' => $employee["Employee"]["internal_id"]))){
                    $this->Session->setFlash('Stramien succesvol verwijderd. De kalenderdagen zijn echter niet gewijzigd. Wilt u dat doen, maakt u een nieuw stramien op.');
                    $this->redirect('/Admin/viewStreams');
                } else {
                    $this->Session->setFlash('Verwijderen van stramien mislukt.');
                    $this->redirect('/Admin/viewStreams');
                }

            }

        } else {
            $this->Session->setFlash('Je moet een geldig stramien opgeven');
            $this->redirect('/Admin/viewStreams');
        }
    }

    public function editStream($id = null){
        $this->set('calendaritemtypes', $this->CalendarItemType->find('all'));
        $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $id)));

        if($this->request->is('post')){
            $incomingStream = $this->request->data;
            if($this->Stream->deleteAll(array('employee_id' => $incomingStream["Stream"]["employee_id"]))){

                $incomingStream = $this->request->data;
                if(count($incomingStream["Stream"]["elements"]) < 20){
                    foreach($incomingStream["Stream"]["elements"] as $key => $element){
                        $key = explode('-', $key);
                        $incomingStream["Stream"]["elements"][$key[0] . '-' . ($key[1] + 5) . '-' . $key[2]] = $element;

                    }
                }

                if($incomingStream["Stream"]["employee_id"] != '-1'){
                    foreach($incomingStream["Stream"]["elements"] as $date => $element){
                        $date = explode('-', $date);
                        $streamObjects[] = array('Stream' => array(
                            'employee_id' => $incomingStream["Stream"]["employee_id"],
                            'calendar_item_type_id' => $element,
                            'relative_nr' => $date[1],
                            'day_time' => $date[2],
                            'day_nr' => date('N', strtotime($date[0]))
                        ));
                    }

                    if($this->Stream->saveMany($streamObjects)){
                        $this->Session->setFlash('Stramien gewijzigd.');
                        $this->redirect('/Admin/viewStreams');
                    }

                } else {
                    $this->Session->setFlash('Je moet een geldige gebruiker opgeven.');
                    $this->redirect('/Admin/viewStreams');
                }

            }
        } else {
            if($id != null){
                $this->set('employee', $employee);
                if(!empty($employee)){
                    $streams = $this->Stream->find('all', array('conditions' => array('employee_id' => $employee["Employee"]["internal_id"])));

                    foreach($streams as $stream){
                        $streamsSorted[strtolower($this->intToDay($stream["Stream"]["day_nr"])) . '-' . $stream["Stream"]["relative_nr"] . '-' . $stream["Stream"]["day_time"]] = array('id' => $stream["Stream"]["id"], 'calendar_item_type_id' => $stream["Stream"]["calendar_item_type_id"], 'element' => strtolower($this->intToDay($stream["Stream"]["day_nr"])) . '-' . $stream["Stream"]["relative_nr"] . '-' . $stream["Stream"]["day_time"]);
                    }

                    $this->set('streams', $streamsSorted);
                }
            } else {
                $this->Session->setFlash('Je moet een geldig stramien opgeven');
                $this->redirect($this->here);
            }
        }
    }

    public function cancelEndOfYear(){
        $this->admin_variable('lockApp', 'write', 'false');
        $this->Session->setFlash('Gelieve Marc Portier te contacteren om de database te herstellen, indien dat nodig zou zijn.');
        $this->redirect('/Admin');
    }

    public function applyStream($id = null){
        if($id != null){
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $id)));
            $streams = $this->Stream->find('all', array('conditions' => array('employee_id' => $employee["Employee"]["internal_id"])));
            $this->set('employee', $employee);
                if(isset($this->request->query["start"])){
                    $departDate = date('Y-m-d', strtotime($this->request->query["start"]));
                } else {
                    $departDate = date('Y-m-d');
                }

            if(!empty($employee)){
                if(isset($this->request->query['apply'])){
                    foreach($streams as $key =>$stream){
                        if($stream["Stream"]["calendar_item_type_id"] == 9){
                            unset($streams[$key]);
                        }
                    }

                    foreach($streams as $stream){
                        if($stream["Stream"]["relative_nr"] > 5){
                            $dateArray = $this->getRange(date('Y-m-d', strtotime($this->getNofYear($stream["Stream"]["day_nr"], 'first', 0) . ' + 7 Days')), $this->getNofYear($stream["Stream"]["day_nr"], 'last', 0), 'ww');
                        } else {
                            $dateArray = $this->getRange($this->getNofYear($stream["Stream"]["day_nr"], 'first', 0), $this->getNofYear($stream["Stream"]["day_nr"], 'last', 0), 'ww');

                        }

                        foreach($dateArray as $key => $date){
                            if($date < $departDate){
                                unset($dateArray[$key]);
                            }
                        }

                        $inserts[] = $this->createManyCalendarDays($dateArray, $stream["Stream"]["calendar_item_type_id"], $employee["Employee"]["internal_id"], $this->Session->read('Auth.Employee.id'), $stream["Stream"]["day_time"]);

                    }

                    $finished = 0;
                    $size = count($inserts);

                    foreach($inserts as $insert){
                        if($this->CalendarDay->saveMany($insert)){
                            $finished++;
                        }
                    }

                    if($size != $finished){
                        $this->Session->setFlash('Niet alle kalendardagen konden worden opgeslagen');
                    }

                    $this->redirect('/Admin/viewStreams');

                }
            }
        }
    }



    //Admin section for Reports
    public function generateReportEmployee($id = null){
        if($id !== null){

            if($id == 0){
                $this->Session->setFlash("Er was geen geldige gebruiker geselecteerd.");
                $this->redirect('/admin/viewEmployees');
            }

            $this->CalendarDay->unBindModel(array('belongsTo' => array('RequestToCalendarDays', 'AuthItem', 'Replacement')));
            if(isset($this->request->query["month"])){
                $month = $this->request->query["month"];
                if($month < 10){
                    $niceMonth = '0' . $month . '-' .date('Y');
                } else {
                    $niceMonth = $month . '-' .date('Y');
                }

                $range = array('start' => date('Y-m-d', strtotime(date('Y') . '-' . $month . '-01')), 'end' => date('Y-m-d', strtotime((date('Y') . '-' . ($month + 1) . '-01') . ' - 1 Day')));
                $this->set('range', $range);
                $conditions = array(
                    'CalendarDay.day_date >=' => $range["start"],
                    'CalendarDay.day_date <' => $range["end"],
                    'Employee.id' => $id
                    );
                $conditionsHolidays = array(
                    'Request.start_date >=' => $range["start"],
                    'Request.end_date <' => $range["end"],
                    'Request.calendar_item_type_id' => 3
                );
                $holidays = $this->Request->find('all', array('conditions' => $conditionsHolidays));
            } else {
                $conditions = array('Employee.id' => $id);
                $range = array('start' =>  date('Y-m-d', strtotime(date('Y') . '-01-01')), 'end' => date('Y-m-d', strtotime(date('Y') . '-12-31')));
                $this->set('range', $range);
                $niceMonth = date('Y');
            }
            $this->set('calendarDays', $this->CalendarDay->find('all', array('conditions' => $conditions, 'order' => 'day_date ASC')));
            $mergedConditions = array_merge($conditions, array('calendar_item_type_id' => 23));
            $this->set('offDays', $this->CalendarDay->find('count', array('conditions' => $mergedConditions, 'order' => 'day_date ASC')));
            $mergedConditions = array_merge($conditions, array('CalendarItemType.dinner_cheque' => 0));
            $this->set('notDinnerCheque', $this->CalendarDay->find('count', array('conditions' => $mergedConditions)));

            $holidayCount = 0;
            if(!empty($holidays)){
                foreach($holidays as $holiday){
                    $holidayCount = $holidayCount + (count($this->dateRange($holiday["Request"]["start_date"], $holiday["Request"]["end_date"], $holiday["Request"]["start_time"], $holiday["Request"]["end_time"]))/2);
                var_dump($holidayCount);
                }
            }
            $workingDays = (count($this->dateRange($range["start"], $range["end"]))/2 - $holidayCount);
            $this->set('workingDays', $workingDays);
            $employee = $this->Employee->findById($id);
            $this->set('employee', $employee);
            $this->set('id', $id);
            $this->pdfConfig = array(
                'orientation' => 'portrait',
                'filename' => 'Werkstaat_' . $employee["Employee"]["name"] . '_' . $employee["Employee"]["surname"] . '_' . $niceMonth
        );

        } else {
            $this->Session->setFlash('Er was geen geldige Employee Id opgegeven om een rapport over op te kunnen stellen');
            $this->redirect(array('controller' => 'admin', 'action' => 'index'));
        }
    }

    public function export() {

        $employees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1', 'Employee.indexed_on_schaubroeck' => true), 'order' => 'Employee.name ASC'));

        if(isset($this->request->query["month"])){
            $month = $this->request->query["month"];
            $range = $this->lastDay($month);

            if(isset($this->request->query["limit"])){
                $limit = $this->request->query["limit"];
                $nicemonth = $month;
                if($month < 10){
                    $niceMonth = '0' . $month ;
                } else {
                    $niceMonth = $month;
                }

                $range[0] = date('Y-m-d', strtotime(date('Y') . '-' . $nicemonth . '-' . $limit));
            }

            $dateRange = $this->dateRange($range[0], $range[1], $starttime = 'AM', $endtime = 'PM', $step = '+1 day', $format = 'Y-m-d', $includeWeekend = true);
            $calendarDays = $this->CalendarDay->find('all', array('conditions' => array('day_date >=' => $range[0], 'day_date <=' => $range[1])));
            foreach ($dateRange as $date){
                if(date('D', strtotime(explode('/', $date)[0])) == 'Sat'){
                    $template[$date] = 'ZA';
                } elseif(date('D', strtotime(explode('/', $date)[0])) == 'Sun'){
                    $template[$date] = 'ZO';
                } else {
                    $template[$date] = 'G';
                }
            }

            foreach($employees as $employee){
                if(isset($this->request->query["type"])){
                    $employeeTemplate[$employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '/' . $employee["Employee"]["id"]] = $template;
                }else{
                    $employeeTemplate[$employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"]] = $template;
                }
            }

            foreach($calendarDays as $calendarDay){
                if(isset($this->request->query["type"])){
                    $data[$calendarDay["Employee"]["name"]  . ' ' . $calendarDay["Employee"]["surname"] . '/' . $calendarDay["Employee"]["id"]][$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay;
                } else {

                    $data[$calendarDay["Employee"]["name"]  . ' ' . $calendarDay["Employee"]["surname"]][$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay["CalendarItemType"]["code"];
                }
            }




            $this->set('dateRange', $dateRange);
            if(!empty($data)){
                $data = array_replace_recursive($employeeTemplate, $data);
                $this->set('data', $data);
            } else {
                $this->Session->setFlash('Er is geen data om te exporteren');
                $this->redirect('/admin/export');
            }


            if(isset($this->request->query["type"])){
                foreach($data as $employeeQuery => $days){
                    $employee = $this->Employee->findById(explode('/', $employeeQuery)[1]);

                    foreach($days as $day => $type){
                        if(!is_array($type)){
                        $daysFull[explode('/',$day)[0]][$employeeQuery . '/' . $employee["Employee"]["internal_id"]][] =  array('time' => explode('/',$day)[1],'type' => $type);
                        } else {
                        $daysFull[explode('/',$day)[0]][$employeeQuery . '/' . $employee["Employee"]["internal_id"]][] =  array('time' => explode('/',$day)[1],'type' => $type["CalendarItemType"]["code"]);
                        }
                    }
                }

                $this->set('daysFull', $daysFull);
            }


            foreach($this->CalendarItemType->find('all') as $calendarType){
                $calendarTypes[$calendarType["CalendarItemType"]["code"]] = $calendarType;
            }



            $this->set('calendarTypes', $calendarTypes);
        }
    }

    public function exportWebView(){
        if(isset($this->request->query["month"])){
            $month = $this->request->query["month"];
            $range = $this->lastDay($month);

        }
    }


    public function lockApp(){
        $status = $this->admin_variable('lockApp', 'find');
        if(empty($this->request->query['action'])){
            $this->request->query['action'] = 'none';

        }
        if($status == 'true'){
            $this->set('link', 'open');
            if($this->request->query["action"] == 'open'){
                $this->admin_variable('lockApp', 'write', 'false');
            }
        }
        else{
            $this->set('link', 'close');
            if($this->request->query["action"] == 'close'){
                $this->admin_variable('lockApp', 'write', 'true');
            }
        }

    }

    public function dinnerCheques(){
        if(isset($this->request->query['month'])){
            $month = $this->request->query['month'];
            $employees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'), 'order' => 'Employee.name ASC'));
            $range = $this->lastDay($month);
            foreach($employees as $employee){
                $eo[] = array('Employee' => array('name' => $employee["Employee"]["name"], 'surname' => $employee["Employee"]["surname"], 'dinner_cheques' => $this->calculateDinnerCheques($employee, $range[0], $range[1])));
            }
            $this->set('employees', $eo);
        }


    }

    public function departments(){
        if($this->request->is('post')){
            $department = $this->request->data;
            if($department["EmployeeDepartment"]["name"] !== ''){
                if($department["EmployeeDepartment"]["id"] == 0){
                    $this->EmployeeDepartment->create();
                    unset($department["EmployeeDepartment"]["id"]);
                    $savedDepartment = $this->EmployeeDepartment->save($department);
                } else {
                    $savedDepartment = $this->EmployeeDepartment->save($department);
                }
                if(!empty($savedDepartment)){
                    $this->Session->setFlash('De dienst ' . $savedDepartment["Department"]["name"] . ' is succesvol opgeslagen.');
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlash('Er liep iets mis bij het opslaan van een nieuwe dienst.');
                    $this->redirect($this->here);
                }
            }
        } else {
            $this->set('departments', $this->EmployeeDepartment->find('all'));
            if(isset($this->request->query["action"])){
                $action = $this->request->query["action"];
                if(isset($this->request->query["id"])){
                    $id = $this->request->query["id"];
                    if($action == 'delete'){
                        $department = $this->EmployeeDepartment->findById($id);
                        if(!empty($department)){
                            if($this->EmployeeDepartment->delete($id)){
                                $this->Session->setFlash('De dienst ' . $department["EmployeeDepartment"]["name"] . ' is succesvol verwijderd.');
                                $this->redirect($this->here);
                            } else {
                                $this->Session->setFlash('Er liep iets mis bij het verwijderen een dienst.');
                                $this->redirect($this->here);
                            }
                        } else {
                            $this->Session->setFlash("Dit departement bestaat niet.");
                            $this->redirect($this->here);
                        }
                    }
                }
            }
        }
    }

    public function addCalendarType(){
        if($this->request->is('post')){
            $calendarType = $this->request->data;
            $this->CalendarItemType->create();
            if($this->CalendarItemType->save($calendarType)){
                $this->redirect($this->here);
            } else {
                $this->Session->setFlash("Het opslaan is mislukt");
                $this->redirect($this->here);
            }
        }
    }

    public function editCalendarTypes(){
        $this->set('calendarTypes', $this->CalendarItemType->find('all'));

        if($this->request->is('post')){
            $calendarTypes = $this->request->data;
            if($this->CalendarItemType->saveMany($calendarTypes)){
                $this->redirect($this->here);
            } else{
                $this->Session->setFlash("Het opslaan is mislukt");
                $this->redirect($this->here);
            }
        }
    }

    public function editCalendarDays(){
        $this->set('crud', false);
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1' ))));
        $this->set('cit', $this->CalendarItemType->find('all'));
        if($this->request->is('post')){
            $icd = $this->request->data["items"]; // Incoming Calendar Days
            $employee = $this->Employee->findById($this->request->data["Crud"]["employee_id"]);

            foreach($icd as $date => $cd){
                foreach($cd as $hour => $type){
                    if(is_array($type)){
                        foreach($type as $id => $object){
                            if($id != 0){
                                $vcd[] = array('CalendarDay' => array('id' => $id, 'employee_id' => $employee["Employee"]["id"],'day_date' => $date, 'day_time' => $hour, 'calendar_item_type_id' => $object["type"])); //Verified Calendar Days
                            } else {
                                if($object['type'] != 0){
                                    $ncd[] = array('CalendarDay' => array('employee_id' => $employee["Employee"]["id"],'day_date' => $date, 'day_time' => $hour, 'calendar_item_type_id' => $object["type"])); //New Calendar Days
                                }
                            }
                        }
                    }
                }
            }

            if($this->CalendarDay->saveMany($vcd)){
                if($this->CalendarDay->saveMany($ncd)){
                    $this->Session->setFlash('Het opslaan van de kalender is geslaagd.');
                } else {
                    $this->Session->setFlash('Het opslaan van de nieuwe kalenderdagen is mislukt.');
                }
            } else {
                $this->Session->setFlash('Het opslaan van de kalender is mislukt.');
            }
            $this->redirect($this->here);
        } else {
            if(isset($this->request->query['month'])){
                $month = $this->request->query['month'];
                if($month < 10){$niceMonth = '0' . $month;} else {$niceMonth = $month;}
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
                if(isset($this->request->query['employee'])){
                    if(isset($this->request->query['year'])){
                        $year = $this->request->query['year'];
                    } else {
                        $year = date('Y');
                    }
                    $employee = $this->request->query['employee'];
                    $this->set('crud', true);

                    $date = array('start' => $year . '-' . $niceMonth .'-01', 'end' => $year . '-' . $niceMonth  . '-' . $daysInMonth);

                    $ucd = $this->CalendarDay->find('all', array('conditions' => array(
                        'day_date >=' => date('Y-m-d', strtotime($date["start"])),
                        'day_date <=' => date('Y-m-d', strtotime($date["end"])),
                        'employee_id' => $employee),
                        'order' => 'day_date ASC'
                    ));

                    $template = $this->dateRange($date["start"], $date["end"]);

                    foreach($template as $cd){
                        $ocd[explode('/',$cd)[0]][explode('/',$cd)[1]] = array('id' => 0, 'name' => '', 'type_id' => 0);
                    }

                    foreach($ucd as $calendarDay){
                        $ocd[$calendarDay["CalendarDay"]["day_date"]][$calendarDay["CalendarDay"]["day_time"]] = array('id' => $calendarDay["CalendarDay"]["id"], 'type_id' => $calendarDay["CalendarDay"]["calendar_item_type_id"], 'name' => $calendarDay["CalendarItemType"]["name"]);
                    }

                    if(isset($ocd)){
                        $this->set('calendarDays', $ocd);
                    } else {
                        $this->set('calendarDays', 'Er is geen resultaat gevonden');
                    }

                }
            }
        }
    }

    private function admin_variable($name, $type = 'write', $value = null){
        $adminVar = $this->AdminVariable->find('first', array('conditions' => array('name' => $name)));
        if($type == 'write'){
            if(empty($adminVar)){
                $this->AdminVariable->create();
                $x = "New Admin Variable created";
                $adminClause = array('AdminVariable' => array('name' => $name, 'value' => $value));
            } else {
                $x = "Admin Variable overwritten";
                $adminClause = $adminVar;
                $adminClause["AdminVariable"]["value"] = $value;
            }

            $this->AdminVariable->save($adminClause);

        } else if($type == 'find'){
            if(!empty($adminVar)){
                $x = $adminVar["AdminVariable"]["value"];
            } else {
                $x = null;
            }

        }

        return $x;
    }

    private function createBackup(){
        $dataSource = ConnectionManager::getDataSource('default');


        ob_start();

        $username = $dataSource->config['login'];
        $password = $dataSource->config['password'];
        $hostname = $dataSource->config['host'];
        $dbname   = $dataSource->config['database'];

        $command = "mysqldump --add-drop-table --host=$hostname --user=$username ";
        if ($password)
            $command.= "--password=". $password ." ";
        $command.= $dbname;
        system($command);

        $dump = ob_get_contents();
        ob_end_clean();
        flush();

        return $dump;

    }

    private function sendSqlAndMail($sql, $body){

        $filename = '/var/www/html/afwezig.westtoer.be/database_exports/' . date('Y-m-d H:i:s');
        file_put_contents($filename, $sql);

        // Send the email to the admin

        $Email = new CakeEmail('westtoer');
        $Email->to(Configure::read('Administrator.email'));
        $Email->subject('A new SQL Backup is made');
        $Email->replyTo('noreply@westtoer.be');
        $Email->from ('noreply@westtoer.be');
        $Email->send($body . $filename);
    }

    private function updateDaysLeft($incomingEmployees){
        foreach($incomingEmployees["Employee"] as $key => $incomingEmployee){
            if($incomingEmployee["daysleft"] !== ""){
                $employees[] = array('Employee' => array('id' => $incomingEmployee["id"], 'daysleft' => $incomingEmployee["daysleft"]));
            }
        }
        $this->Employee->saveMany($employees);
    }

    private function getNOfYear($daynr, $type, $year=1){
        $daysofweek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        $month = array('first' => 'January', 'last' => 'December');
        $n = date("Y-m-d", strtotime($type . " " . $daysofweek[$daynr-1] ." of " . $month[$type] ." ". (date('Y') + $year).""));
        return $n;
    }

    private function getRange($start, $end, $step = "w"){
        $dateRange = '';
        $stepper = array('w' => '7', 'ww' => '14');
        $x = $start;

        while($x <= $end){
            $dateRange[] = date('Y-m-d', strtotime($x . ' + ' . $stepper[$step] .' Days'));
            $x = date('Y-m-d', strtotime($x . ' + ' . $stepper[$step] .' Days'));
        };

        return $dateRange;
    }

    private function createManyCalendarDays($dateArray, $calendaritemtype, $employeeId, $supervisorId, $type = 'day'){
        $items = array();
        $this->Request->create();
        $request = $this->Request->save(array('employee_id' => $employeeId, 'name' => 'Stramien', 'start_date' => date('Y-m-d', strtotime(date('Y') . '-01-01')), 'start_time' => 'AM', 'end_date' => date('Y-m-d', strtotime(date('Y') . '-01-01')), 'end_time' => 'AM','timestamp' => date('Y-m-d'), 'calendar_item_type_id' => $calendaritemtype, 'replacement_id' => '-1'));
        $this->AuthItem->create();
        $authItem = array('AuthItem' => array('request_id' => $request["Request"]["id"], 'supervisor_id' => $supervisorId, 'authorized' => 1, 'authorization_date' => date('Y-m-d H:i:s'), 'message' => 'Stream '));
        $savedAuthItem = $this->AuthItem->save($authItem);
        if(!empty($savedAuthItem)){
            foreach($dateArray as $date){
                $items[]['CalendarDay'] = array('employee_id' => $employeeId, 'calendar_item_type_id' => $calendaritemtype, 'replacement_id' => 4, 'day_date' => $date, 'day_time' => $type);
            }
        }
        return $items;

    }

    private function calculateDinnerCheques($employee, $start, $end){
        $offDays = array();
        $workDays = count($this->dateRange($start, $end))/2;
        $result = (int)$workDays;
        $substract = $this->CalendarDay->find('all', array('conditions' =>
            array(
                'CalendarItemType.dinner_cheque' => 0,
                'Employee.id' => $employee["Employee"]["id"],
                'CalendarDay.day_date >=' => date('Y-m-d', strtotime($start)),
                'CalendarDay.day_date <=' => date('Y-m-d', strtotime($end))
            )
            )
        );



        foreach($substract as $date){
            $offDays[$date["CalendarDay"]["day_date"]][$date["CalendarDay"]["day_time"]] = true;
        }
        foreach($offDays as $offDay){
            if(count($offDay) == 2){
                $result--;
            }
        }
        return $result;
    }

    private function dateRange( $first, $last, $starttime = 'AM', $endtime = 'PM', $step = '+1 day', $format = 'Y-m-d', $includeWeekend = false){
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
                if($includeWeekend == false){
                    if(date('D', $current) == 'Sat' or date('D', $current) == 'Sun'){
                        $current = strtotime( $step, $current );
                    } else {
                        $dates[] = date( $format, $current );
                        $current = strtotime( $step, $current );
                    }
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

    private function lastDay($month){
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
        $range = array(date('Y-m-d', strtotime(date('Y') .'-' . $month .'-01')), date('Y-m-d', strtotime(date('Y') .'-' . $month .'-' . $daysInMonth)));
        return $range;
    }

    private function intToDay($key){
        $key = $key -1;
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        return $days[$key];
    }
}