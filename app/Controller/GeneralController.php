<?php
App::uses('AppController', 'Controller');
class GeneralController extends AppController{

    public $helpers = array('Employee', 'CalendarDay');
    public $uses = array('Employee', 'Request', 'CalendarDay');
    public function index(){
        $this->set('holidays', $this->Request->find('all', array(
            'conditions' => array(
                'Request.start_date >=' => date('Y-m-d'),
                'Request.calendar_item_type_id' => 3,
                'Request.employee_id' => 4
            ), 'limit' => 5, 'order' => 'Request.start_date ASC'
        )));
        $absences = $this->CalendarDay->find('all', array(
            'conditions' => array(
                'CalendarDay.day_date' => date('Y-m-d'),
                'AuthItem.authorized' => 1,
                'CalendarDay.calendar_item_type_id <> ' => 9
            ), 'order' => 'day_date ASC, CalendarDay.employee_id ASC'
        ));

        //Todays absences
        $absences = $this->setAbsences($absences, 'today');
        $this->set('absences', $absences);

        //This week's absences
        $beginWeek = date('Y-m-d', strtotime('this week', time(date('Y-m-d'))));
        $endWeek = date('Y-m-d', strtotime($beginWeek . ' + 4 Days'));

        $absencesThisWeek = $this->CalendarDay->find('all', array(
            'conditions' => array(
                'CalendarDay.day_date >=' => $beginWeek,
                'CalendarDay.day_date <=' => $endWeek,
                'AuthItem.authorized' => 1,
                'CalendarDay.calendar_item_type_id <> ' => 9
            ), 'order' => 'day_date ASC'
        ));
        $absencesThisWeek = $this->sortOverDay($absencesThisWeek, $beginWeek, $endWeek);
        $this->set('absencesThisWeek', $absencesThisWeek);

    }

    private function setAbsences($absences, $type = 'today'){
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
                        if($absences[$key + 1]["CalendarDay"]["day_time"] == 'PM'){

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
        $sorted[date('Y-m-d', strtotime($start))] = '';
        $sorted[date('Y-m-d', strtotime($start . ' + 1 Day'))] = '';
        $sorted[date('Y-m-d', strtotime($start . ' + 2 Day'))] = '';
        $sorted[date('Y-m-d', strtotime($start . ' + 3 Day'))] = '';
        $sorted[date('Y-m-d', strtotime($end))] = '';

        foreach($calendarDays as $calendarDay){
            $sorted[$calendarDay["CalendarDay"]["day_date"]][$calendarDay["CalendarDay"]["day_time"]][] = $calendarDay["Employee"];
        }
        $sorted["range"] = array('start' => $start, 'end' => $end);
        return $sorted;
    }
}