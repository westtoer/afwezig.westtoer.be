<?php
class CalendarDayHelper extends AppHelper{
    public function tableCalendarDays($calendarDays){
        $html =  '<div class="week">';
        $html .= '<div class="spacer"></div>';
        $html .='<table class="week">';
        $html .= '<tr class="titlerow"><th>Maandag('. date('d-m', strtotime($calendarDays["range"]["start"])) .')</th><th>Dinsdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 1 Day')) .')</th><th>Woensdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 2 Days')) .')</th><th>Donderdag ('. date('d-m', strtotime($calendarDays["range"]["start"] . '+ 3 Days')) .')</th><th>Vrijdag ('. date('d-m', strtotime($calendarDays["range"]["end"])) .')</th></tr>';
        $html .= '<tr class="am">';
        $html .='<td>';
            //var_dump()



        $html .= '</td>';
        '<td></td><td></td><td></td><td></td></tr>';
        $html .= '<tr class="pm"><td></td><td></td><td></td><td></td><td></td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;

    }

}