<?php
App::uses('ConnectionManager', 'Model');
App::uses('CakeEmail', 'Network/Email');
App::import('Controller', 'Requests');

class AdminController extends AppController {

    public $uses = array('User', 'Employee', 'Request', 'EmployeeDepartment', 'RequestToCalendarDay', 'CalendarDay', 'AdminVariable', 'AuthItem', 'Stream', 'CalendarItemType', 'Export','EmployeeCount');
    public $helpers = array('Employee', 'Request', 'CalendarDay', 'CalendarItemType', 'Xls');
    public $components = array('RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
        if($this->Session->read('Auth.Role.id') !== '1'){
            if($this->Session->read('Auth.Role.id') !== '2'){
                $this->Session->setFlash('Je hebt geen rechten in het administratiepaneel.', 'default', array('class' => 'alert-danger'));
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
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.status' => 1, 'Employee.internal_id <>' => '-1'))));
        $this->set('');
        if($this->request->is('post')){

            $this->Employee->create();
            $employee = $this->request->data;
            $existing = $this->Employee->find('first', array('conditions' => array('Employee.name' => $employee["Employee"]["name"], 'Employee.surname' => $employee["Employee"]["surname"], 'Employee.internal_id' => $employee["Employee"]["internal_id"])));
            if(empty($existing)){
                if($this->Employee->save($employee)){
                    $this->Session->setFlash('Gebruiker is opgeslagen.');
                } else {
                    $this->Session->setFlash('Het opslaan van de gebruiker is mislukt.', 'default', array('class' => 'alert-danger'));
                }
            } else {
                $this->Session->setFlash("Er bestaat al een gebruiker met dezelfde naam en personeelsnummer", 'default', array('class' => 'alert-danger'));
            }
            $this->redirect('/Admin/viewEmployees');
        }
    }

    public function viewEmployees(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'), 'order' => 'Employee.name ASC')));

    }

    public function editEmployee($id = null){
        if($this->request->is('post')){
            $employee = $this->request->data;
            $this->Employee->save($employee);
            $this->Session->setFlash('Gebruiker gewijzigd.');
            $this->redirect(array('action' => 'viewEmployees'));
        } else {
            if($id !== null){
                $employee = $this->Employee->findById($id);
                $this->set('employee', $employee);
                $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1', 'Employee.status' => 1))));
                $this->set('departments', $this->EmployeeDepartment->find('all'));
                $this->set('prevCost', $this->CalendarDay->find('count', array('conditions' => array('CalendarDay.employee_id' => $employee["Employee"]["id"], 'CalendarDay.calendar_item_type_id' => 23))));
            } else {
                $this->Session->setFlash('Je hebt geen geldige gebruiker geselecteerd.', 'default', array('class' => 'alert-danger'));
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
        $authorizer = $this->Employee->findById($this->Session->read('Auth.Employee.id'));

        if($this->request->is('post')){
            $request = $this->request->data;
                $request["Request"]["employee_id"] = 4;
                if($request["Request"]["type"] == 0){
                    $request["Request"]["calendar_item_type_id"] = 3;
                } elseif($request["Request"]["type"] == 1){
                    $request["Request"]["calendar_item_type_id"] = 23;
                } elseif($request["Request"]["type"] == 2) {
                    $request["Request"]["calendar_item_type_id"] = 6;
                }

                $request["Request"]["timestamp"] = date('Y-m-d H:i:s');
                $request["Request"]["replacement_id"] = '-1';
                $request["Request"]["auth_item_id"] = 4;
                $this->Request->create();
                if($request["Request"]["start_date"] < date('Y-m-d', strtotime(date('Y') . '-12-31'))){
                    $savedRequest = $this->Request->save($request);
                } else {
                    $this->Session->setFlash("Je kan enkel Feestdagen voor dit jaar ingegeven", 'default', array('class' => 'alert-danger'));
                    $this->redirect($this->here);
                }
            $this->AuthItem->create();
            $authItem = array('request_id' => $savedRequest["Request"]["id"], 'supervisor_id' => $authorizer["Employee"]["internal_id"], 'authorization_date' => date('Y-m-d H:i:s'), 'message' => "Holiday at request " . $savedRequest["Request"]["id"]);
            $authItem = $this->AuthItem->save($authItem);
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
                $streams = $this->Stream->find('first', array('conditions' => array('employee_id' => $employee["Employee"]["internal_id"])));
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
                        $this->Session->setFlash('Het stramien kon niet worden opgeslagen.', 'default', array('class' => 'alert-danger'));
                        $this->redirect($this->here);
                    }
                } else {
                    $this->Session->setFlash('Deze gebruiker heeft al een stramien.', 'default', array('class' => 'alert-danger'));
                    $this->redirect($this->here);
                }
            } else {
                $this->Session->setFlash('Je moet een geldige gebruiker opgeven.', 'default', array('class' => 'alert-danger'));
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
                    $this->Session->setFlash('Verwijderen van stramien mislukt.', 'default', array('class' => 'alert-danger'));
                    $this->redirect('/Admin/viewStreams');
                }

            }

        } else {
            $this->Session->setFlash('Je moet een geldig stramien opgeven', 'default', array('class' => 'alert-danger'));
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
                    $this->Session->setFlash('Je moet een geldige gebruiker opgeven.', 'default', array('class' => 'alert-danger'));
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
        $this->Session->setFlash('Gelieve Marc Portier te contacteren om de database te herstellen, indien dat nodig zou zijn.', 'default', array('class' => 'alert-danger'));
        $this->redirect('/Admin');
    }

    public function applyStream($id = null){
        if($id != null){
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $id)));
            $streams = $this->Stream->find('all', array('conditions' => array('Employee_id' => $employee["Employee"]["internal_id"])));
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
                            if(isset($count)){
                                $dateArray = $this->getRange(date('Y-m-d', strtotime($this->getNofYear($stream["Stream"]["day_nr"], 'first', 0) . ' + 7 Days')), $this->getNofYear($stream["Stream"]["day_nr"], 'last', 0), 'ww');
                            }
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
                        $this->Session->setFlash('Niet alle kalenderdagen konden worden opgeslagen', 'default', array('class' => 'alert-danger'));
                    }

                    $this->redirect('/Admin/viewStreams');

                }
            }
        }
    }



    //Admin section for Reports
    public function generateReportEmployee($id = null){
        if($id != null){

            //We don't need all fields for the report, let's strip some out on the Query
            $this->CalendarDay->unBindModel(array('belongsTo' => array('RequestToCalendarDays', 'AuthItem', 'Replacement')));

            //If a month is set, narrow a the range.
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

            //Find every object needed for the report
            $employee = $this->Employee->findById($id);
            $this->set('calendarDays', $this->CalendarDay->find('all', array('conditions' => $conditions, 'order' => 'day_date ASC')));
            $mergedConditions = array_merge($conditions, array('calendar_item_type_id' => 23));
            $this->set('offDays', $this->CalendarDay->find('count', array('conditions' => $mergedConditions, 'order' => 'day_date ASC')));
            $holidayCount = 0;

            if(!empty($holidays)){
                foreach($holidays as $holiday){
                    $holidayCount = $holidayCount + (count($this->dateRange($holiday["Request"]["start_date"], $holiday["Request"]["end_date"], $holiday["Request"]["start_time"], $holiday["Request"]["end_time"]))/2);
                var_dump($holidayCount);
                }
            }

            //Calculate how many days employees have worked.
            $workingDays = (count($this->dateRange($range["start"], $range["end"]))/2 - $holidayCount);
            $this->set('workingDays', $workingDays);

            //Expose the employee to the view
            $this->set('employee', $employee);
            $this->set('id', $id);

            //Allow the pdf to be created.
            $this->pdfConfig = array(
                'orientation' => 'portrait',
                'filename' => 'Werkstaat_' . $employee["Employee"]["name"] . '_' . $employee["Employee"]["surname"] . '_' . $niceMonth
        );
            $this->set('dinnerCheques', $this->calculateDinnerCheques($employee, $range["start"], $this->lastDay(date('m'))));
        } else {
            $this->Session->setFlash('Er was geen geldige Employee Id opgegeven om een rapport over op te kunnen stellen', 'default', array('class' => 'alert-danger'));
            $this->redirect(array('controller' => 'admin', 'action' => 'index'));
        }
    }

    public function export() {
        //Iterate over calendarTypes
        foreach($this->CalendarItemType->find('all') as $calendarType){
            $calendarTypes[$calendarType["CalendarItemType"]["code"]] = $calendarType;
        }

        //Define month
        if(isset($this->request->query["month"])){
            $month = $this->request->query["month"];
            $niceMonth = $month;
            if($month < 10){
                if(substr($month, 0 ,1) != '0'){
                    $niceMonth = '0' . $month;
                }
            }

            $range["end"] = $this->lastDay($month);
            $range["templateStart"] = date('Y-m-d', strtotime(date('Y') . '-' . $niceMonth . '-01' ));

            if(!isset($this->request->query["type"])){
                if(isset($this->request->query["limit"])){
                    $limit = $this->request->query["limit"];
                    if($limit < 10){
                        $limit = '0' . $limit;
                    }
                   $range["start"] =  date('Y-m-d', strtotime(date('Y') . '-' . $niceMonth . '-' . $limit ));
                   $range["templateStart"] = $range["start"];
                } else {
                    $range["start"] = date('Y-m-d', strtotime(date('Y') . '-' . $niceMonth . '-01' ));
                }
            }

            //Get previous export date (that isn't ignored) and get all record from before.
            $prevExport = $this->Export->find('first', array('conditions' => array('Export.ignored' => false), 'order' => 'Export.timestamp DESC'));
            if(isset($this->request->query["type"])){
                if(!empty($prevExport)){
                    $limit = $prevExport["Export"]["timestamp"];
                    $range["start"] = date('Y-m-d H:i:s', strtotime($limit));
                    $niceMonth = $month;

                    if($range["start"] > $range["templateStart"]){
                        $this->Session->setFlash('Je kan geen tweede geldige export doen van een maand. Wil je dit toch, moet je de vorige ongeldig verklaren', 'default', array('class' => 'alert-danger'));
                        $this->redirect('/Admin/export');
                    }
                } else {
                    $range["start"] = '2012-01-01';
                }
            }

            //Webview still shows employees that aren't indexed on Schaubroeck
            if(!isset($this->request->query["webview"])){
                $options = array('Employee' => array('Employee.status' => 1, 'Employee.internal_id <>' => '-1', 'Employee.indexed_on_schaubroeck' => true), 'CalendarDay' => array('OR' => array(array('last_update >=' => $range["start"]), array('last_update' => '0000-00-00 00:00:00')), 'day_date <=' => $range["end"], 'Employee.indexed_on_schaubroeck' => 1));
            } else {
                $options = array('Employee' => array('Employee.status' => 1, 'Employee.internal_id <>' => '-1'), 'CalendarDay' => array('day_date >=' => $range["start"], 'day_date <=' => $range["end"]));
            }

            //Data dependancies
            $employees = $this->Employee->find('all', array('conditions' => $options["Employee"], 'order' => 'Employee.name ASC'));
            $dateRange = $this->dateRange($range["templateStart"], $range["end"], $starttime = 'AM', $endtime = 'PM', $step = '+1 day', $format = 'Y-m-d', $includeWeekend = true);
            $calendarDays = $this->CalendarDay->find('all', array('conditions' => $options["CalendarDay"], 'order' => 'day_date ASC'));


            //Create a template month with no off days
            foreach ($dateRange as $date){
                if(date('D', strtotime(explode('/', $date)[0])) == 'Sat'){
                    $template[$date] = 'ZA';
                } elseif(date('D', strtotime(explode('/', $date)[0])) == 'Sun'){
                    $template[$date] = 'ZO';
                } else {
                    $template[$date] = 'G';
                }
            }

            //Apply the template to each employee
            foreach($employees as $employee){
                if(isset($this->request->query["type"])){
                    $employeeTemplate[$employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '/' . $employee["Employee"]["id"]] = $template;
                }else{
                    $employeeTemplate[$employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"]] = $template;
                }
            }

            //Create the actual roster
            foreach($calendarDays as $calendarDay){
                if(isset($this->request->query["type"])){
                    $data[$calendarDay["Employee"]["name"]  . ' ' . $calendarDay["Employee"]["surname"] . '/' . $calendarDay["Employee"]["id"]][$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay;
                } else {
                    $data[$calendarDay["Employee"]["name"]  . ' ' . $calendarDay["Employee"]["surname"]][$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay["CalendarItemType"]["code"];
                }
            }

            //Give the GUI access to the date range
            $this->set('dateRange', $dateRange);

            //Merge the template with the actual roster
            if(!empty($data)){
                $data = array_replace_recursive($employeeTemplate, $data);
                $this->set('data', $data);
            } else {
                $this->set('data', $employeeTemplate); // If no roster was found, output the template
                $data = $employeeTemplate;
            }



            //Write the calendarDays to a JSON file and create an export record (if this isn't a webview)
            if(isset($this->request->query["type"])){
                //Get the export dir path
                $date = date('Y-m-d') . 'T' . date('H:i:s');
                $exportPath = Configure::read('Administrator.export_dir') . '/json/' . $date . '.json';

                //Create a database record for exports
                $this->Export->create();
                $export = array('Export' => array('timestamp' => $date, 'json_path' => $exportPath, 'xls_path' => 'null', 'ignored' => false, 'start_date' => $range["templateStart"], 'end_date' => $range["end"], 'employee_id' => $this->Session->read('Auth.Employee.id')));
                $export = $this->Export->save($export);

                //Write a json file with only the calendar days (not working days)
                if(!empty($data)){
                    $JSON = json_encode(array($export, array('data' => $data)));
                } else {
                    $JSON = json_encode(array($export, array('data' => $employeeTemplate)));
                }

                $file = new File($exportPath, true);
                $file->write($JSON);
            }

            //Gather data and write CSV
            if(isset($this->request->query["type"])){
                $exportCsv = Configure::read('Administrator.export_dir') . '/csv/' . $date . '.csv';
                $file = new File($exportCsv, true);


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

                $header = array('westtoernummer,', 'NummerPersoneelslid,', 'Datum,', 'aard_schaubroeck,', 'code_schaubroeck,', 'extensie_schaubroeck,', 'uur');
                $x = '';
                foreach($header as $element){
                    $x .= $element;
                }
                $x .= "\n";

                foreach($daysFull as $day  => $daycontent){
                    foreach($daycontent as $employee => $employeecontents){
                        if($employeecontents[0]["type"] != "ZA" and $employeecontents[0]["type"] != "ZO" and $employeecontents[1]["type"] != "ZA" and $employeecontents[1]["type"] != "ZO"){
                            if($employeecontents[0]["type"] == $employeecontents[1]["type"]){
                                $value = 7.6;
                                $x .= '752,';
                                $x .= explode('/', $employee)[2] . ',';
                                $x .= $day . ',';
                                $x .= $calendarTypes[$employeecontents[0]["type"]]["CalendarItemType"]["aard_schaubroek"] . ',';
                                $x .= $calendarTypes[$employeecontents[0]["type"]]["CalendarItemType"]["code_schaubroek"] . ',';
                                $x .= $calendarTypes[$employeecontents[0]["type"]]["CalendarItemType"]["ext_schaubroek"] . ',';
                                $x .= $value;
                                $x .= "\n";
                            } else {
                                $value = 3.8;
                                foreach($employeecontents as $content){
                                    $x .= '752,';
                                    $x .= explode('/', $employee)[2] . ',';
                                    $x .= $day . ',';
                                    $x .= $calendarTypes[$content["type"]]["CalendarItemType"]["aard_schaubroek"] . ',';
                                    $x .= $calendarTypes[$content["type"]]["CalendarItemType"]["code_schaubroek"] . ',';
                                    $x .= $calendarTypes[$content["type"]]["CalendarItemType"]["ext_schaubroek"] . ',';
                                    $x .= $value;
                                    $x .= "\n";
                                }
                            }
                        }
                    }
                }

                $file->write($x);
                $export["Export"]["xls_path"] = $exportCsv;
                if($this->Export->save($export)){
                    $this->Session->setFlash('Export opgeslagen op ' . $exportPath . ' en '. $exportCsv);
                } else {
                    $this->Session->setFlash('De export kon niet worden verwerkt.', 'default', array('class' => 'alert-danger'));
                }
                $this->redirect('/Admin/export');


            }

            //Add Calendar Types for GUI

            $this->set('calendarTypes', $calendarTypes);
        }
    }

    public function ignoreExports(){
        if(isset($this->request->query["id"])){
            if(isset($this->request->query["ignore"])){
                $export = $this->Export->findById($this->request->query["id"]);
                $export["Export"]["ignored"] = true;
                if($this->Export->save($export)){
                    $this->Session->setFlash('Export wordt vanaf nu genegeerd');
                } else {
                    $this->Session->setFlash('Negeren van export mislukt', 'default', array('class' => 'alert-danger'));
                }
                $this->redirect('/Admin/ignoreExports');
            }
        } else {
            $this->set('exports', $this->Export->find('all', array('conditions' => array('Export.ignored' => false))));
            $this->set('exportsIgnored', $this->Export->find('all', array('conditions' => array('Export.ignored' => true))));
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
                $this->Session->setFlash('Je hebt de applicatie gesloten. Gebruikers kunnen nu niet meer aanmelden.', 'default', array('class' => 'alert-danger'));
                $this->redirect($this->here);
            }
        }
        else{
            $this->set('link', 'close');
            if($this->request->query["action"] == 'close'){
                $this->admin_variable('lockApp', 'write', 'true');
                $this->Session->setFlash('De applicatie is terug klaar voor gebruik.');
                $this->redirect($this->here);
            }
        }

    }

    public function dinnerCheques(){

        //Show the monthcount if month is set.
        if(isset($this->request->query['month'])){
            $month = $this->request->query['month'];
            $employees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1', 'Employee.dinner_cheques' => 1), 'order' => 'Employee.name ASC'));
            $range[0] = $this->firstDay($month);
            $range[1] = $this->lastDay($month);

            //Calculate how many dinner cheques an employee gets this month
            foreach($employees as $employee){
                $eo[$employee["Employee"]["id"]] = array('Employee' => array('name' => $employee["Employee"]["name"], 'surname' => $employee["Employee"]["surname"], 'dinner_cheques' => $this->calculateDinnerCheques($employee, $range[0], $range[1])));
            }

            //Expose it to the view
            $this->set('employees', $eo);

            //Check if we can show the Persist in Database button
            $lastPersist = $this->admin_variable('lastPersist', 'find');
            $showPersist = true;

            if(date('Y-m', strtotime($lastPersist)) > date('Y-m', strtotime(date('Y') . '-' . $month . '-01'))){
                $showPersist = false;
            }

            //Tell the view
            $this->set('showPersist', $showPersist);

            //The user initatiated a persist in the database.
            if(isset($this->request->query["persist"])){
                if($this->request->query["persist"] == true){

                    //Check if this month is the correct month to persist
                    if($month > date('m')){
                        $this->Session->setFlash('Je kan geen data persisteren voor maanden die nog moeten komen');
                        $this->redirect($this->here);
                    }

                    //Change the count for every user
                    //Every user has a count, because if calculateDinnerCheques doesn't find a record, it creates one.
                    foreach($employees as $employee){
                        $counter = $this->EmployeeCount->find('first', array('conditions' => array('EmployeeCount.employee_id' =>$employee["Employee"]["id"])));
                        $counter["EmployeeCount"]["dinner_cheques"] = $counter["EmployeeCount"]["dinner_cheques"] + $eo[$employee["Employee"]["id"]]["Employee"]["dinner_cheques"];
                        $counters[] = $counter;
                    }

                    //Save all counters
                    if($this->EmployeeCount->saveMany($counters)){

                        //Let the system know there a new lastPersist in town
                        $this->admin_variable('lastPersist','write', date('Y-m'));

                        //Notify and redirect
                        $this->Session->setFlash('Data opgeslagen in de database');
                    } else {
                        $this->Session->setFlash('Opslaan mislukt');
                    }

                    $this->redirect($this->here);
                }
            }
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
                    $this->Session->setFlash('Er liep iets mis bij het opslaan van een nieuwe dienst.', 'default', array('class' => 'alert-danger'));
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
                                $this->Session->setFlash('Er liep iets mis bij het verwijderen een dienst.', 'default', array('class' => 'alert-danger'));
                                $this->redirect($this->here);
                            }
                        } else {
                            $this->Session->setFlash("Dit departement bestaat niet.", 'default', array('class' => 'alert-danger'));
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
                $this->Session->setFlash("Het opslaan is mislukt", 'default', array('class' => 'alert-danger'));
                $this->redirect($this->here);
            }
        }
    }

    public function editCalendarTypes(){
        $this->set('calendarTypes', $this->CalendarItemType->find('all'));

        if($this->request->is('post')){
            $calendarTypes = $this->request->data;
            if($this->CalendarItemType->saveMany($calendarTypes["existing"])){
                if(isset($calendarTypes["new"])){
                    if($this->CalendarItemType->saveMany($calendarTypes["new"])){

                    }
                }
            } else{
                $this->Session->setFlash("Het opslaan is mislukt", 'default', array('class' => 'alert-danger'));
            }
            $this->redirect($this->here);
        }
    }

    public function editCalendarDays(){
        $this->set('crud', false);
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1' ), 'order' => 'Employee.surname ASC')));
        $this->set('cit', $this->CalendarItemType->find('all'));
        $vcd = array();
        $ncd = array();
        if($this->request->is('post')){
            $icd = $this->request->data["items"]; // Incoming Calendar Days
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $this->request->data["Crud"]["employee_id"])));

            foreach($icd as $date => $cd){
                foreach($cd as $hour => $type){
                    if(is_array($type)){
                        foreach($type as $id => $object){
                            if($id != 0){
                                $vcd[] = array('CalendarDay' => array('id' => $id, 'employee_id' => $employee["Employee"]["id"],'day_date' => $date, 'day_time' => $hour, 'calendar_item_type_id' => $object["type"])); //Verified Calendar Days
                            } else {
                                if($object['type'] != 0){
                                    $ncd[] = array('CalendarDay' => array('employee_id' => $employee["Employee"]["id"],'day_date' => $date, 'day_time' => $hour, 'calendar_item_type_id' => $object["type"], 'replacement_id' => '-1')); //New Calendar Days
                                }
                            }
                        }
                    }
                }
            }

            $this->Session->setFlash('Het opslaan van de kalender is geslaagd.');
            if(!empty($vcd)){
                if($this->CalendarDay->saveMany($vcd)){

                } else {
                    $this->Session->setFlash('Het opslaan van de kalender is mislukt.', 'default', array('class' => 'alert-danger'));
                }
            }
            if(!empty($ncd)){
                if($this->CalendarDay->saveMany($ncd)){

                } else {
                    $this->Session->setFlash('Het opslaan van de kalender is mislukt.', 'default', array('class' => 'alert-danger'));
                }
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
                    $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $employee, 'Employee.internal_id <>' => '-1')));
                    $this->set('crud', true);

                    $date = array('start' => $year . '-' . $niceMonth .'-01', 'end' => $year . '-' . $niceMonth  . '-' . $daysInMonth);

                    if(!empty($employee)){
                        $ucd = $this->CalendarDay->find('all', array('conditions' => array(
                            'day_date >=' => date('Y-m-d', strtotime($date["start"])),
                            'day_date <=' => date('Y-m-d', strtotime($date["end"])),
                            'employee_id' => $employee["Employee"]["id"]),
                            'order' => 'day_date ASC'
                        ));
                    } else {
                        $this->Session->setFlash('Ongeldige gebruiker geselecteerd.', 'default', array('class' => 'alert-danger'));
                        $this->redirect('/Admin');
                    }

                    $template = $this->dateRange($date["start"], $date["end"]);

                    foreach($template as $key => $date){
                        if(date('D', strtotime(explode('/', $date)[0])) == "Sat" or date('D', strtotime(explode('/', $date)[0])) == "Sun"){
                            unset($template[$key]);
                        }
                    }

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

    public function addManyCalendarDays(){
        $this->set('types', $this->CalendarItemType->find('all'));
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1', 'Employee.status' => 1), 'order' => 'Employee.name ASC')));

        if($this->request->is('post')){
            $ir = $this->request->data; //Incoming Request
            $Request = new RequestsController;
            $Request->constructClasses();

            $sr = $Request->addRequest($ir); //Saved Request
            if($ir["Request"]["destination"] == true){
                $Request->authorize($sr["Request"]["id"], 'allow');
            }
            $this->Session->setFlash('Succesvol gewijzigd.');
            $this->redirect($this->here);

        }
    }

    public function changeSupervisor(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.status' => 1))));
        if($this->request->is('post')){
            $data = $this->request->data;
            $employees = $this->Employee->find('all', array('conditions' => array('Employee.supervisor_id' => $data["Supervisor"])));
            if($data["Replacement"] != '-1'){
                if(!empty($employees)){
                    foreach($employees as $key => $employee){
                        $employees[$key]["Employee"]["supervisor_id"] = $data["Replacement"];
                    }
                }

                if($this->Employee->saveMany($employees)){
                    $this->Session->setFlash('Verantwoordelijke succesvol gewijzigd.');
                } else {
                    $this->Session->setFlash('Opslaan mislukt.', 'default', array('class' => 'alert-danger'));
                }

                $this->redirect($this->here);
            }
        }
    }

    public function viewAuthorisations(){
        if(isset($this->request->query["employee"])){
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.internal_id' => $this->request->query["employee"])));
            $this->set('aiAccepted', $this->AuthItem->find('all', array('conditions' => array('AuthItem.authorized' => 1, 'Request.employee_id' => $employee["Employee"]["id"]))));
            $this->set('aiDenied', $this->AuthItem->find('all', array('conditions' => array('AuthItem.authorized' => 0, 'AuthItem.authorization_date <>' => null, 'Request.employee_id' => $employee["Employee"]["id"]))));
            $this->set('aiDenied', $this->AuthItem->find('all', array('conditions' => array('AuthItem.authorization_date' => null, 'Request.employee_id' => $employee["Employee"]["id"]))));
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

        $filename = Configure::read('Administrator.export_dir') . '/backup/' . date('Y-m-d H:i:s');
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

    private function calculateDinnerCheques($employee, $start, $end, $year = null){
        if($year == null){
            $year = date('Y');
        }

        //Lookup the employee's stats
        $employeeCount = $this->EmployeeCount->find('first', array('conditions' => array('EmployeeCount.employee_id' => $employee["Employee"]["id"], 'year' => date('Y'))));

        //If no stats a present, create a new record
        if(empty($employeeCount)){
            $this->EmployeeCount->create();
            $employeeCount = array('EmployeeCount' => array('employee_id' => $employee["Employee"]["id"], 'year' => date('Y'), 'dinner_cheques' => 0));
            $employeeCount = $this->EmployeeCount->save($employeeCount);
        }

        //Find the datespan of this month
        $datesMonth = $this->dateRange($start, $end);
        foreach($datesMonth as $key => $date){
            if(date('D', strtotime(explode('/', $date)[0])) == 'Sat' or date('D', strtotime(explode('/', $date)[0])) == 'Sun'){
                unset($datesMonth[$key]);
            }
        }

        //Find out how many Dinner Cheques should be given this month
        $substr = $this->CalendarDay->find('count', array('conditions' => array('day_date >=' => $start, 'day_date <=' => $end, 'CalendarDay.employee_id' => $employee["Employee"]["id"], 'CalendarItemType.dinner_cheque' => 0)));
        $template = count($datesMonth) / 2;
        $add = $template - $substr;

        unset($substr, $template);

        //Find the datespan of this month
        $datesYear= $this->dateRange($year . '-01-01', $end);
        foreach($datesYear as $key => $date){
            if(date('D', strtotime(explode('/', $date)[0])) == 'Sat' or date('D', strtotime(explode('/', $date)[0])) == 'Sun'){
                unset($datesYear[$key]);
            }
        }

        //Find out how many Dinner Cheques an employee should have
        $substr = $this->CalendarDay->find('all', array('conditions' => array('day_date >=' => $year . '01-01', 'day_date <=' => $year . '-12-31', 'CalendarDay.employee_id' => $employee["Employee"]["id"], 'CalendarItemType.dinner_cheque' => 0)));
        foreach($substr as $calendarDay){
            $valid[$calendarDay["CalendarDay"]["day_date"]][] = $calendarDay;
        }

        $substr = 0;

        if(!empty($valid)){
            foreach($valid as $day){
                if(count($day) > 1){
                    $substr++;
                }
            }
        }

        $template = count($datesYear) / 2;
        $should = $template - $substr;

        //Check if the persisted data is equal to what it should be
        $penalty = 0;
        if($should  < $employeeCount["EmployeeCount"]["dinner_cheques"]){
            $penalty = $employeeCount["EmployeeCount"]["dinner_cheques"] - $should;
        }

        $add = $add - $penalty;

        return $add;
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

    private function firstDay($month){
        $range = date('Y-m-d', strtotime(date('Y') .'-' . $month .'-01'));
        return $range;
    }

    private function lastDay($month){
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
        $range = date('Y-m-d', strtotime(date('Y') .'-' . $month .'-' . $daysInMonth));
        return $range;
    }

    private function intToDay($key){
        $key = $key -1;
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        return $days[$key];
    }
}