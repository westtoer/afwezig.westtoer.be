<?php
class StreamHelper extends AppHelper{

    public function tableStreams($streams){
        $html = '<table class="table">';
        $html .= '<tr><th>Gekoppeld aan</th><th>Type</th><th>Reden</th><th>Acties</th></tr>';
        foreach($streams as $stream){
            $html .= '<tr>';
            $html .= '<td>' . $stream["Employee"]["name"] . ' ' . $stream["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $this->convertType($stream["Stream"]["rule_type"], $stream["Stream"]["day_time"]) . '</td>';
            $html .= '<td>' . $stream["CalendarItemType"]["name"] . '</td>';
            $html .= '<td>' . '<a href="' . $this->base .'/admin/removeStream/' . $stream["Stream"]["id"] .'">'  . 'Verwijder</a></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    private function convertType($rule_type, $day_time){
        if($rule_type = 'w'){
            $a[0] = 'Wekelijks';
        } else {
            $a[0] = 'Tweewekelijks';
        }

        if($day_time !== 'day'){
            $a[1] = '1/5';
        } else {
            $a[1] = '1/10';
        }

        return $a[0] . '-' . $a[1];
    }
}