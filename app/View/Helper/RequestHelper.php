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


    public function tableRequests($requests, $type = 'user'){
        $html = '<table class="table">';
        if($type == 'user'){
            $html .= '<tr><th>Start</th><th>Einde</th>';
            $html .= '<th>Reden</th><th>Vervanger</th><th>Goedgekeurd?</th></tr>';
        } elseif($type == 'admin'){
            $html .= '<th>Naam</th><th>Type</th><th>Vervanger</th><th>Start</th><th>Einde</th><th>Acties</th></tr>';
        } else {
            $html .= '<tr><th>Start</th><th>Einde</th>';
            $html .= '<th>Naam</th><th>Acties</th></tr>';
        }

        foreach($requests as $request){
            $html .= '<tr>';
            if($type == 'user'){
                $html .= '<td>' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . '</td>';
                $html .= '<td>' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '</td>';
                $html .= '<td>' . $request["CalendarItemType"]["name"] . '</td>';
                $html .= '<td><a href="' . $this->base . '/employees/view/' .$request["Replacement"]["id"] . '">' . $request["Replacement"]["name"] . " " . $request["Replacement"]["surname"] . '</a></td>';
                $html .= '<td>' . $this->isApproved($request["AuthItem"]["authorized"], $request["AuthItem"]["authorization_date"], true) . '</td></tr>';
            } elseif($type == 'admin'){
                $html .= '<td>' . $request["Employee"]["name"] . ' ' . $request["Employee"]["surname"] . '</td>';
                $html .= '<td>' . $request["CalendarItemType"]["name"] . '</td>';
                $html .= '<td><a href="' . $this->base . '/employees/view/' .$request["Replacement"]["id"] . '">' . $request["Replacement"]["name"] . " " . $request["Replacement"]["surname"] . '</a></td>';
                $html .= '<td>' . $request["Request"]["start_date"] . ' ' . $request["Request"]["start_time"] . '</td>';
                $html .= '<td>' . $request["Request"]["end_date"] . ' ' . $request["Request"]["end_time"] . '</td>';
                $html .= '<td><a href="' . $this->base .'/Request/overlap/' . $request["Request"]["id"] . '">Overlap</a>  |  <a href="' . $this->base .'/Request/allow/' . $request["Request"]["id"] . '">Goedkeuren</a>  |  <a href="' . $this->base .'/Request/deny/' . $request["Request"]["id"] . '">Weigeren</a>';
            } else {
                $html .= '<td>' . $request["Request"]["start_date"] . '</td>';
                $html .= '<td>' . $request["Request"]["end_date£"] . '</td>';
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

    public function tableOverlap($overlap, $queryRange){

        foreach($queryRange as $date){
            $query[explode('/', $date)[1]][explode('/', $date)[0]]["Query"] = array('Employee' => array('name' => 'Desbetreffende persoon', 'surname' => ''), 'CalendarItemType' => array('id' => 23));
        }

        $count = count($overlap["AM"]);

        $overlap = array_merge_recursive($query, $overlap);

            $html = '<div class="week"><table class="week">';
            foreach($overlap as $key => $timeblock){
                ksort($timeblock);
                if($key == 'AM'){
                    $html .= '<tr class="titlerow"><th></th>';
                    foreach($timeblock as $day => $date_desc){
                        $html .= '<th><strong>' . $day . '</strong></th>';
                    }
                    $html .= '</tr>';
                }
                $html .= '<tr class="' . strtolower($key) . '"><td width="50px"><center>' . $key .'</center></td>';
                foreach($timeblock as $day => $date_desc){

                    if(!empty($date_desc["Query"])){
                        $html .= '<td class="overlap" width="' . (98 / $count) . '%">';
                    } else {
                        $html .= '<td width="' . (98 / $count) . '%">';
                    }
                    foreach($date_desc as $employeeKey => $employee){

                        if($employee["CalendarItemType"]["id"] != 9){
                            $html .= '<div class="content">';
                            if($employeeKey !== 'Query'){
                                $html .= '<div class="calendarline">';
                                $html .= $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '  -  ' . $employee["CalendarItemType"]["name"];
                            }
                            $html .= '</div></div>';
                        }
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table></div>';

        return $html;

    }


}
