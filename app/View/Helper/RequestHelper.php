<?php
class RequestHelper extends AppHelper {
    public function isApproved($value, $manufactured = 0, $short = false){

        if(print_r($manufactured, true) == '0000-00-00 00:00:000' or $manufactured == null){
            if($short == true){
                $x = 'In aanvraag';
            } else {
                $x = 'Dit verlof is nog niet goedgekeurd';
            }
        } else{
            if($value == 0){
                if($short == true){
                    $x = "Nee";
                } else {
                    $x = 'Dit verlof is geweigerd';
                }
            } else {
                if($short == true){
                    $x = 'Ja';
                } else {
                    $x = "Dit verlof is goedgekeurd";
                }
            }
        }


        return $x;
    }



    public function selectorAllTypes($types, $datatype = 'html'){
        if($datatype == 'html'){
            //Give a standard null option
            $typesOptions[] = '<option value="0">Type Verlof</option>';
            //Fill with all Calendar Item Types
            foreach($types as $type){
                $typesOptions[] = '<option value="' . $type["CalendarItemType"]["id"] . '">' .$type["CalendarItemType"]["code"] . '  -  '. $type["CalendarItemType"]["name"] . "</option>";
            }
        } elseif($datatype == 'array'){
            //Give a standard null option
            $typesOptions[] = array('name' => 'Kies uw type afwezigheid', 'value' => 0);
            //Fill with all Calendar Item Types
            foreach($types as $type){
                $typesOptions[] = array('name' => $type["CalendarItemType"]["code"] . '  -  '. $type["CalendarItemType"]["name"],
                    'value' => $type["CalendarItemType"]["id"]);

            }
        }
        return $typesOptions;
    }


    public function tableRequests($requests, $type = 0){
        $html = '<table class="table">';
        $html .= '<tr><th>Start</th><th>Einde</th>';
        if($type == 0){
            $html .= '<th>Reden</th><th>Vervanger</th><th>Goedgekeurd?</th></tr>';
        } else {
            $html .= '<th>Naam</th><th>Acties</th></tr>';
        }

        foreach($requests as $request){
            $html .= '<tr>';
            $html .= '<td>' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . '</td>';
            $html .= '<td>' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '</td>';
            if($type == 0){
                $html .= '<td>' . $request["CalendarItemType"]["name"] . '</td>';
                $html .= '<td><a href="' . $this->base . '/employees/view/' .$request["Replacement"]["id"] . '">' . $request["Replacement"]["name"] . " " . $request["Replacement"]["surname"] . '</a></td>';
                $html .= '<td>' . $this->isApproved($request["AuthItem"]["authorized"], $request["AuthItem"]["authorization_date"], true) . '</td></tr>';
            } else {
                $html .= '<td>' . $request["Request"]["name"] . '</td>';
                $html .= '<td><a href="' . $this->base .'/Admin/GeneralCalendarItems/action:delete/id:'. $request["Request"]["id"] .'">Verwijder</a></td></tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }

    public function replacementToName($replacementId, $employees){
        do {
            foreach($employees as $possiblereplacement){
                if($replacementId == $possiblereplacement['Employee']['id']){
                    $replacement = $possiblereplacement;
                };
            };
        } while(empty($replacement));

        return $replacement;
    }

    public function globalCalendarItems($CalendarItemsGlobal){
        $html = '<ul class="nulled">';
        if(!empty($CalendarItemsGlobal)){
            foreach($CalendarItemsGlobal as $CalendarItem){
                if($CalendarItem["CalendarItem"]["start_date"] == $CalendarItem["CalendarItem"]["end_date"]){
                    $date = $CalendarItem["CalendarItem"]["start_date"];
                } else{
                    $date = $CalendarItem["CalendarItem"]["start_date"] . ' ' . $CalendarItem["CalendarItem"]["start_time"] . '  -  ' . $CalendarItem["CalendarItem"]["end_date"] . $CalendarItem["CalendarItem"]["end_time"];
                }
                $html .= '<li>' . $CalendarItem["CalendarItem"]["note"] . ' (' . $date . ')</li>';
                unset($date);
            }
        $html .= '</ul>';
        return $html;
        } else {
            return 'Geen algemene feestdagen';
        }
    }


}
