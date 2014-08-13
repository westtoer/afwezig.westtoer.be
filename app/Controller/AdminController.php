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
            $existing = $this->Employee->find('first', array('conditions' => array('Employee.name' => $employee["Employee"]["name"], 'Employee.surname' => $employee["Employee"]["surname"], 'Employee.telephone' => $employee["Employee"]["telephone"])));
            if(empty($existing)){
                $this->Employee->save($employee);
            } else {
                $this->Session->setFlash("Er bestaat al een gebruiker met dezelfde naam en telefoonnummer");
                $this->redirect('/admin');
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
            $this->redirect(array('action' => 'viewUsers'));
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

    /*public function Roles(){
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
    }*/

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
                $request["Request"]["replacement_id"] = 4;
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
                        'replacement_id' => 4,
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
                    $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'))));
                    $this->set('calendaritemtypes', $this->CalendarItemType->find('all'));
                } elseif($step == '9'){
                   $incomingStreams = $this->request->data;
                   foreach($incomingStreams as $incomingStream){
                       $this->Stream->create();
                       $savedStream = $this->Stream->save($incomingStream);
                       $authorizer = $this->Session->read('Auth.Employee.id');

                       $calendarDays = $this->createManyCalendarDays(
                                $this->getRange($this->getNOfYear($savedStream["Stream"]["day_relative"], 'first'),  $this->getNOfYear($savedStream["Stream"]["day_relative"], 'last'), $incomingStream["Stream"]["rule_type"]),
                                    $savedStream["Stream"]["calendar_item_type_id"], $savedStream["Stream"]["employee_id"], $authorizer, $savedStream["Stream"]["id"], $incomingStream["Stream"]["day_time"]);
                       $this->CalendarDay->saveMany($calendarDays);
                       $this->redirect('/admin/endOfYear?step=10');
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

    public function addStreams(){
        $this->set('employees', $this->Employee->find('all', array('conditions' => array('Employee.id <> 4'))));
        $this->set('calendaritemtypes', $this->CalendarItemType->find('all'));

        $incomingStreams = $this->request->data;
        foreach($incomingStreams as $incomingStream){
            $this->Stream->create();
            $savedStream = $this->Stream->save($incomingStream);
            $authorizer = $this->Session->read('Auth.Employee.id');

            $calendarDays = $this->createManyCalendarDays(
                $this->getRange($this->getNOfYear($savedStream["Stream"]["day_relative"], 'first'),  $this->getNOfYear($savedStream["Stream"]["day_relative"], 'last'), $incomingStream["Stream"]["rule_type"]),
                $savedStream["Stream"]["calendar_item_type_id"], $savedStream["Stream"]["employee_id"], $authorizer, $savedStream["Stream"]["id"], $incomingStream["Stream"]["day_time"]);
            if($this->CalendarDay->saveMany($calendarDays)){
                $this->Session->setFlash('Stramienen zijn successvol opgeslagen');
                $this->redirect('/admin');
            } else {
                $this->Session->setFlash('Er liep iets mis bij het opslaan van de stramienen. Probeer het later nog eens');
                $this->redirect('/admin');
            };
        }
    }

    public function viewStreams(){
        $this->set('streams', $this->Stream->find('all'));
    }

    public function removeStream($id = null){
        if($id !== null){
            $stream = $this->Stream->findById($id);
            if(!empty($stream)){
                $range = $this->getRange($this->getNOfYear($stream["Stream"]["day_relative"], 'first'),  $this->getNOfYear($stream["Stream"]["day_relative"], 'last'), $stream["Stream"]["rule_type"]);
                $calendarDays = array('');

                foreach($range as $date){
                    $loader = array('day_date' => $date);
                    $or[] = $loader;
                }

                if($stream["Stream"]["day_time"] == 'day' or $stream["Stream"]["day_time"] == null){
                    $conditions = array('CalendarDay.employee_id' => $stream["Stream"]["employee_id"], 'calendar_item_type_id' => $stream["Stream"]["calendar_item_type_id"], 'OR' => $or);
                } else {
                    $conditions = array('CalendarDay.employee_id' => $stream["Stream"]["employee_id"], 'calendar_item_type_id' => $stream["Stream"]["calendar_item_type_id"], 'day_time' => $stream["Stream"]["day_time"], 'OR' => $or);
                }

                $calendarDays = $this->CalendarDay->find('all', array('conditions' => $conditions));
                if(!empty($calendarDays)){
                    foreach($calendarDays as $calendarDay){
                        $calendarDay["CalendarDay"]["calendar_item_type_id"] = 9;
                        $changedCalendarDays[] = $calendarDay;
                    }

                    if($this->CalendarDay->saveMany($changedCalendarDays)){
                        if($this->Stream->delete($id)){
                            $this->Session->setFlash('Het stramien is succesvol verwijderd.');
                            $this->redirect('/admin');
                        } else {
                            $this->Session->setFlash('Er liep iets mis bij het verwijderen van het stramien.');
                            $this->redirect('/admin');
                        }
                    } else {
                        $this->Session->setFlash('Er liep iets mis bij het wijzigen van de kalendardagen van deze gebruiker.');
                        $this->redirect('/admin');
                    };
                } else {
                    $this->Session->setFlash('Dit stramien heeft geen dagen gekoppelt.');
                    $this->redirect('/admin');
                }
            }
        } else {
            $this->Session->setFlash('Er is geen geldige Stramien-id opgegeven.');
            $this->redirect('/admin');
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

        $employees = $this->Employee->find('all', array('conditions' => array('Employee.internal_id <>' => '-1'), 'order' => 'Employee.name ASC'));

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

                $data = array_merge_recursive($employeeTemplate, $data);
                $this->set('data', $data);
            } else {
                $this->Session->setFlash('Er is geen data om te exporteren');
                $this->redirect('/admin/export');
            }


            if(isset($this->request->query["type"])){
                foreach($data as $employeeQuery => $days){
                    $employee = $this->Employee->findById(explode('/', $employeeQuery)[1]);

                    foreach($days as $day => $type){
                        $daysFull[explode('/',$day)[0]][$employeeQuery . '/' . $employee["Employee"]["internal_id"]][] =  array('time' => explode('/',$day)[1],'type' => $type[0]);
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
        } else {
            $this->Session->setFlash('Maaltijdcheques kunnen enkel per maand berekend worden.');
            $this->redirect('/admin');
        }


    }

    public function departments(){
        if($this->request->is('post')){
            $department = $this->request->data;
            if($department["Department"]["name"] !== ''){
                $this->EmployeeDepartment->create();
                $savedDepartment = $this->EmployeeDepartment->save($department);
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
                                $this->Session->setFlash('De dienst ' . $department["Department"]["name"] . ' is succesvol verwijderd.');
                                $this->redirect($this->here);
                            } else {
                                $this->Session->setFlash('Er liep iets mis bij het verwijderen een diensts.');
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

    private function getNOfYear($daynr, $type){
        $daysofweek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        $month = array('first' => 'January', 'last' => 'December');
        $n = date("Y-m-d", strtotime($type . " " . $daysofweek[$daynr-1] ." of " . $month[$type] ." ". (date('Y') + 1).""));
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

    private function createManyCalendarDays($dateArray, $calendaritemtype, $employeeId, $supervisorId, $streamId, $type = 'day'){
        $this->AuthItem->create();
        var_dump($type);
        $authItem = array('AuthItem' => array('request_id' => '0', 'supervisor_id' => '0', 'authorized' => 1, 'authorization_date' => date('Y-m-d H:i:s'), 'message' => 'Stream ' . $streamId));
        $savedAuthItem = $this->AuthItem->save($authItem);
        if(!empty($savedAuthItem)){
            foreach($dateArray as $date){
                if($type == 'day'){
                    $items[] = array('CalendarDay' => array('employee_id' => $employeeId, 'calendar_item_type_id' => $calendaritemtype, 'auth_item_id' => $savedAuthItem["AuthItem"]["id"], 'replacement_id' => 4, 'request_to_calendar_days_id' => 0, 'day_date' => $date, 'day_time' => 'AM'));
                    $items[] = array('CalendarDay' => array('employee_id' => $employeeId, 'calendar_item_type_id' => $calendaritemtype, 'auth_item_id' => $savedAuthItem["AuthItem"]["id"], 'replacement_id' => 4, 'request_to_calendar_days_id' => 0, 'day_date' => $date, 'day_time' => 'PM'));
                } elseif($type == 'AM' or $type == 'PM') {
                    $items[] = array('CalendarDay' => array('employee_id' => $employeeId, 'calendar_item_type_id' => $calendaritemtype, 'auth_item_id' => $savedAuthItem["AuthItem"]["id"], 'replacement_id' => 4, 'request_to_calendar_days_id' => 0, 'day_date' => $date, 'day_time' => $type));
                }
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
}