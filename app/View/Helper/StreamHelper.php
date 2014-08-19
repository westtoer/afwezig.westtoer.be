<?php
class StreamHelper extends AppHelper{

    public function tableStreams($employees){
        $html = '<table class="table">';
        $html .= '<tr><th width="200px">Gekoppeld aan</th><th>Acties</th></tr>';
        foreach($employees as $employee){
            $html .= '<tr><td>' . $employee["Employee"]["name"] . ' ' .  $employee["Employee"]["surname"] . '</td><td>';
            $html .= '<a href="/Admin/editStream/' . $employee["Employee"]["internal_id"] .'">Pas aan</a>  |  ';
            $html .= '<a href="/Admin/applyStream/' . $employee["Employee"]["internal_id"] .'">Pas toe</a>  |  ';
            $html .= '<a href="/Admin/removeStream/'  . $employee["Employee"]["internal_id"] .'">Verwijder</a>';
            $html .='</td></tr>';
        }

        return $html;
    }


}