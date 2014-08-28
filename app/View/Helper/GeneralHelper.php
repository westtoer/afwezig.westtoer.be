<?php
class GeneralHelper extends AppHelper{

    public function absenceToList($type, $absences){
        $html = '';
        if(array_key_exists($type, $absences)){
            $html = '';
            if(!empty($absences)){
                foreach($absences[$type] as $calendarDay){;
                    $html .= '<li class="list-group-item">';
                    $html .= $calendarDay["Employee"]["name"] . ' ' . $calendarDay["Employee"]["surname"] . $this->typeToPhrase($type);
                    if($calendarDay["Replacement"]["internal_id"] != '-1' and $calendarDay["Replacement"]["id"] != null){
                        $html .= ' <a href="' .$this->base . '/employees/view' . $calendarDay["Replacement"]["id"] . '">'.$calendarDay["Replacement"]["name"] . ' ' . $calendarDay["Replacement"]["surname"] . '</a> neemt alle taken over.';
                    }
                    $html .= '</li>';
                }
            }
        }

        return $html;
    }

    private function typeToPhrase($type){
        switch($type){
            case 'AM':
                $phrase = ' is in de ochtend afwezig.';
                break;
            case 'PM':
                $phrase = ' is in de namiddag afwezig.';
                break;
            case 'Day':
                $phrase = ' is de hele dag afwezig.';
                break;
            default:
                $phrase ='';
                break;
        }

        return $phrase;
    }
}