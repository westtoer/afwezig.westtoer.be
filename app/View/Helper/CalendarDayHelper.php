<?php
class CalendarDayHelper extends AppHelper{
    public function tableCalendarDays($calendarDays){
        $html =  '<div class="week"><div class="spacer"></div><table class="week">';
        $html .= $this->a_titlerow($calendarDays);
        $html .= '<tr class="am">';
        $html .= $this->a_blocks($calendarDays, 'AM');
        $html .= '</tr><tr class="pm">';
        $html .= $this->a_blocks($calendarDays, 'PM');
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;

    }

    private function a_titlerow($calendarDays){
        return '<tr class="titlerow"><th>Maandag('. date('d-m', strtotime($calendarDays["range"]["start"])) .')</th><th>Dinsdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 1 Day')) .')</th><th>Woensdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 2 Days')) .')</th><th>Donderdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 3 Days')) .')</th><th>Vrijdag ('. date('d-m', strtotime($calendarDays["range"]["end"])) .')</th></tr>';
    }

    private function a_blocks($calendarDays, $externalKey){
        $html ='';
        foreach($calendarDays as $day => $calendarDay){
            foreach($calendarDay as $key => $section){
                if(count($section) <= 5){
                    $html .= $this->fillBlock($key, $externalKey, $section);
                } else {
                    $html .= $this->fillBlock($key, $externalKey, $section, $day, 5);
                }

            }
        }
        return $html;
    }

    private function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d', $starttime = 'AM', $endtime = 'PM'){
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
                if(date('D', $current) == 'Sat'){
                    $current = strtotime( $step, $current );
                } elseif(date('D', $current) == 'Sun'){
                    $current = strtotime( $step, $current );
                }else {
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

    private function fillBlock($key, $externalKey, $section, $day = null, $limit = 20){
        $html = '';
        if($key == $externalKey){
            $html .= '<td>';
            $html .= '<div class="content">';
            if($section !== ''){
                $x = 1;
                    foreach($section as $employee){
                        if($x <= $limit){
                        $html .= '<div class="calendarline">' . $employee["name"] . ' ' . $employee["surname"] . '</div>';
                        $x++;
                        }
                }

                if(isset($day)){
                    $html .= '<div class="calendarline red"><a href="' . $this->base .'/CalendarDays/absences/' . $day .'">Bekijk alle</a></div>';
                }
            }
            $html .= '</div>';
            $html .= '</td>';
        }
        return $html;
    }

    public function report($calendarDays, $range){
        $complex = $this->generateReportStructure($calendarDays, $range["start"], $range["end"]);
        $html = '<table class="table">';
        $count = 0;
        foreach($complex["items"] as $key => $date){
            $count++;
            if(date('D', strtotime(explode('/',$key)[0])) == 'Mon' or $count < 2){
                if(explode('/',$key)[1] == 'AM'){
                    if(explode('-', explode('/',$key)[0])[2] == '01'){

                    } else {
                        $html .= '<tr class="newweek"><td></td><td></td><td></td></tr>';
                    }
                }
                }

                if(explode('/',$key)[1] == 'AM'){
                    $html .= '<tr class="daystart">';
                    $html .= '<td width="150px">' . explode('/',$key)[0] . '</td>';
                } else {
                    $html .= '<tr class="dayend">';
                    $html .= '<td></td>';
                }

                $html .= '<td>' . explode('/',$key)[1] . '</td>';
                if($date !== ''){
                    $html .= '<td>' . $date["CalendarItemType"]["name"] .'</td>';
                } else {
                    $html .= '<td>Gewerkt</td>';
                }
                $html .= '</tr>';
            }
        $html .= '</table>';

        return array('size' => $complex["size"], 'html' => $html);
    }

    public function reportAll($calendarDays, $range){

    }

    private function generateReportStructure($calendarDays, $start, $end){
        $dateRange = $this->dateRange($start, $end);
        $convertedCalendar = array();
        $structure = array();
        $size = sizeof($calendarDays);
        foreach($calendarDays as $calendarDay){
            $convertedCalendar[$calendarDay["CalendarDay"]["day_date"] . '/' . $calendarDay["CalendarDay"]["day_time"]] = $calendarDay;
        }



        if(isset($this->request->query["type"])){
           if($this->request->query["type"] == 'off'){
               $convertedCalendar = array('size' => $size, 'items' => $convertedCalendar);
               return $convertedCalendar;
           }
        } else {
            foreach($dateRange as $date){
                $structure[$date] = '';
            }

            $merged = array_merge($structure, $convertedCalendar);
            $data = array('size' => $size, 'items' => $merged);
            return $data;
        }
    }

    public function toMonthLocale($id){
        $months = array('','Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');
        return $months[$id];
    }

}

