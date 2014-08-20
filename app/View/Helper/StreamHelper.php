<?php
class StreamHelper extends AppHelper{

    public $helpers = array('CalendarItemType');

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

    public function copyStreams($streams){
        $html = '<table class="table">';
        $html .= '<tr><th width="20px"></th><th>Naam</th></tr>';
        foreach($streams as $stream){
            $html .= '<tr><td><input type="checkbox" id="copy-' . $stream["Stream"]["employee_id"] . '" name="data[Stream][' . $stream["Employee"]["internal_id"] . ']"></td><td>' . $stream["Employee"]["name"] . ' ' . $stream["Employee"]["surname"] . '</td></tr>';
        }
        $html .= '</table>';

        return $html;
    }

    public function addStream($calendaritemtypes, $type = 0, $employee = '-1'){
        //Building blocks
        $elements = array('weekOne' => array('monday-1',  'tuesday-2',  'wednesday-3',  'thursday-4',  'friday-5'), 'weekTwo' => array('monday-6', 'tuesday-7', 'wednesday-8', 'thursday-9', 'friday-10'));
        $hours = array('AM', 'PM');
        $weeks = array('weekOne', 'weekTwo');
        $subhtml = '';

        if($type == 0){
            $employee = '';
        }
        foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){$subhtml .= $option;};

        //HTML
        $html = '';

            foreach($weeks as $week){
                $html .= '<div class="week"><table class="table week">';
                $html .= '<tr><th></th><th width="20%">Maandag</th><th width="20%">Dinsdag</th><th width="20%">Woensdag</th><th width="20%">Donderdag</th><th width="20%">Vrijdag</th></tr>';
                foreach($hours as $hour){
                    $html .= '<tr class="'. strtolower($hour) .'"><td>'. strtoupper($hour) .'</td>';
                    foreach($elements[$week] as $element){
                        $html .= '<td>';
                        $html .= '<select id="' . $element . '-' . strtoupper($hour) .'" name="data';
                        if($type == 1){
                            if($employee !== '-1'){
                                $html .= '[' . $employee .']';
                            }
                        }
                        $html .= '[Stream][elements][' . ucfirst($element) . '-' . strtoupper($hour) .']" class="form-control ' . $week .'"';
                        if($week == 'weekOne'){
                            $html .= 'OnChange="updateSecondWeek()">';
                        } else{
                            $html .= '>';
                        }
                        $html .= $subhtml;
                        $html .= '</select></td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</table>';
                $html .= '</div>';
            }

        return $html;
        }
}