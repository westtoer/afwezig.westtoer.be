<?php
class CalendarDayHelper extends AppHelper{
    public function tableCalendarDays($calendarDays){
        $html =  '<div class="week"><div class="spacer"></div><table class="week">';
        $html .= '<tr class="titlerow"><th>Maandag('. date('d-m', strtotime($calendarDays["range"]["start"])) .')</th><th>Dinsdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 1 Day')) .')</th><th>Woensdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 2 Days')) .')</th><th>Donderdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 3 Days')) .')</th><th>Vrijdag ('. date('d-m', strtotime($calendarDays["range"]["end"])) .')</th></tr>';
        $html .= '<tr class="am">';
        $html .= $this->a_blocks($calendarDays, 'AM');
        $html .= '</tr><tr class="pm">';
        $html .= $this->a_blocks($calendarDays, 'PM');
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;

    }

    private function a_titlerow($range){




    }

    private function a_blocks($calendarDays, $externalKey){
        $html ='';
        foreach($calendarDays as $calendarDay){
            foreach($calendarDay as $key => $section){
                if($key == $externalKey){
                    $html .= '<td>';
                    $html .= '<div class="content">';
                    if($section !== ''){
                        foreach($section as $employee){
                            $html .= '<div class="calendarline">' . $employee["name"] . ' ' . $employee["surname"] . '</div>';
                        }
                    }
                    $html .= '</div>';
                    $html .= '</td>';
                }
            }
        }
        return $html;
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

