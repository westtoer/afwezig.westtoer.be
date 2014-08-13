<?php
class CalendarDaysController extends AppController{

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('');
    }

    public $helpers = array('General');

    public function absences($day = null){
        if($day !== null){
            $absences = $this->CalendarDay->find('all',
                array(
                    'conditions' => array(
                        'CalendarDay.day_date' => $day,
                        'CalendarDay.calendar_item_type_id <>'  => 9,
                        'AuthItem.authorized' => 1
                    )
                ));
            $absences = $this->setAbsences($absences, 'today');
            $this->set('absences', $absences);
            $this->set('day', $day);
        } else {
            $this->redirect('/');
        }
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
}