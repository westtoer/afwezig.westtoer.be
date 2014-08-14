<?php
App::uses('AppController', 'Controller');
class GeneralController extends AppController{

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('');
    }

    public $helpers = array('Employee', 'CalendarDay');
    public $uses = array('Employee', 'Request', 'CalendarDay', 'RequestToCalendarDay');
    public function index(){

        $this->set('holidays', $this->Request->find('all', array(
            'conditions' => array(
                'AuthItem.authorized' => 1,
                'Request.start_date >=' => date('Y-m-d'),
                'Request.calendar_item_type_id' => 3,
                'Request.employee_id' => 4
            ), 'limit' => 5, 'order' => 'Request.start_date ASC'
        )));
        $absences = $this->CalendarDay->find('all', array(
            'conditions' => array(
                'CalendarDay.day_date' => date('Y-m-d'),
                'CalendarDay.calendar_item_type_id <> ' => 9
            ), 'order' => 'day_date ASC, CalendarDay.employee_id ASC'
        ));

        //Todays absences
        $absences = $this->setAbsences($absences, 'today');
        $this->set('absences', $absences);

        //This week's absences
        if(isset($this->request->query["start"])){
            if(date('D', strtotime($this->request->query["start"])) !== 'Mon'){
                $this->redirect('/');
            } else {
                $beginWeek = date('Y-m-d', strtotime($this->request->query["start"]));
                $endWeek = date('Y-m-d', strtotime($beginWeek . ' + 4 Days'));
            }
        } else {
            $beginWeek = date('Y-m-d', strtotime('this week', time(date('Y-m-d'))));
            $endWeek = date('Y-m-d', strtotime($beginWeek . ' + 4 Days'));
        }

        $navigate = array('previous' => date('Y-m-d', strtotime($beginWeek . ' - 7 Days')), 'next' => date('Y-m-d', strtotime($beginWeek . ' + 7 Days')));

        $this->set('navigate', $navigate);

        $absencesThisWeek = $this->CalendarDay->find('all', array(
            'conditions' => array(
                'CalendarDay.day_date >=' => $beginWeek,
                'CalendarDay.day_date <=' => $endWeek,
                'CalendarDay.calendar_item_type_id <> ' => 9
            ), 'order' => 'day_date ASC'
        ));
        $absencesThisWeek = $this->sortOverDay($absencesThisWeek, $beginWeek, $endWeek);
        $this->set('absencesThisWeek', $absencesThisWeek);

        $this->set('nextRequest', $this->Request->find('first', array(
            'conditions' => array(
                'AuthItem.authorized' => 1,
                'Request.employee_id' => $this->Session->read('Auth.Employee.id'),
                'Request.start_date >' => date('Y-m-d')
            ), 'order' => 'Request.start_date ASC'
        )));

    }

    private function setAbsences($absences, $type = 'today'){
        $absenceTable = array('');
        if($type == 'today'){
            foreach($absences as $key => $absence){
                $next = false;

                if($key !== 0){
                    if($absence["Employee"]["id"] == $absences[$key - 1]["Employee"]["id"]){
                        $absenceTable["Day"][] = $absence;
                        $next = true;
                    } else {
                        if($absence["CalendarDay"]["day_time"] == 'AM'){
                            if($absences[$key + 1]["CalendarDay"]["day_time"] == 'PM'){

                            } else {
                                $absenceTable["AM"][] = $absence;
                            }
                        } else {
                            $absenceTable["PM"][] = $absence;
                        }
                    }
                } else{
                    if($absence["CalendarDay"]["day_time"] == 'AM'){
                        if(($key + 1) < count($absences)){
                            if($absences[$key + 1]["CalendarDay"]["day_time"] == 'PM'){

                            } else {
                                $absenceTable["AM"][] = $absence;
                            }
                        } else {
                            $absenceTable["AM"][] = $absence;
                        }
                    } else {
                        $absenceTable["PM"][] = $absence;
                    }
                }
            }
        }
        return $absenceTable;
    }

    private function sortOverDay($calendarDays, $start, $end){
        $range = $this->dateRange($start, $end);
        foreach($range as $day){
            $sorted[$day] = array('AM' => '', 'PM' => '');
        }
        foreach($calendarDays as $calendarDay){
            $sorted[$calendarDay["CalendarDay"]["day_date"]][$calendarDay["CalendarDay"]["day_time"]][] = $calendarDay["Employee"];
        }
        $sorted["range"] = array('start' => $start, 'end' => $end);
        return $sorted;
    }

    private function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ){
        $dates = '';
        $current = strtotime( $first );
        $last = strtotime( $last );

            while( $current <= $last ) {
                $dates[] = date( $format, $current );
                $current = strtotime( $step, $current );
            }

        return $dates;
    }
}