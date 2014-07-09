<?php
class CalendarItemHelper extends AppHelper {
    public function isApproved($value){
        if($value == 0){
            $x = "Dit verlof is nog niet goedgekeurd";
        } elseif($value == 1){
            $x = "Dit verlof is goedgekeurd";
        } else {
            $x = "Er is iets mis in de database";
            $x .= "Expected 0 or 1, given: " . $value;
        }

        return $x;
    }


    public function selectorAllEmployees($employees, $datatype = 'html', $placement = 0){
        //Give a standard null option
        if($datatype == 'html'){
            if($placement== 1){
                $employeesOptions[] = '<option value="0">Gebruiker</option>';
            } else {
                $employeesOptions[] = '<option value="0">Kies een vervanger</option>';
            }
            //Fill with all Employees
            foreach($employees as $employee){
                $employeesOptions[] = '<option value="' .$employee["Employee"]["id"] . '">' . $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '</option>';
            }
        } elseif($datatype == 'array'){
            //Give a standard null option
            $employeesOptions[] = array('name' => 'Kies uw vervanger (optioneel)', 'value' => 0);
            //Fill with all Employees
            foreach($employees as $employee){
                $employeesOptions[] = array('name' => $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"],
                    'value' => $employee["Employee"]["id"]);
            }
        }

        return $employeesOptions;
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

    public function selectorAllEmployeeDepartments($employeeDepartments, $datatype = 'html', $placement = 0){
        //Give a standard null option
        if($datatype == 'html'){

                $employeesOptions[] = '<option value="0">Dienst</option>';

            //Fill with all Employees
            foreach($employeeDepartments as $employeeDepartment){
                $employeesOptions[] = '<option value="' .$employeeDepartment["EmployeeDepartment"]["id"] . '">' . $employeeDepartment["EmployeeDepartment"]["name"] . '</option>';
            }
        } elseif($datatype == 'array'){
            //Give a standard null option
            $employeesOptions[] = array('name' => 'Dienst', 'value' => 0);
            //Fill with all Employees
            foreach($employeeDepartments as $employeeDepartment){
                $employeesOptions[] = array('name' => $employeeDepartment["EmployeeDepartment"]["name"],
                    'value' => $employeeDepartment["EmployeeDepartment"]["id"]);
            }
        }

        return $employeesOptions;
    }

    public function tableCalendarItems($InputCalendarItems, $date, $employees){
        if($date == 'day'){
            $html = '<h2>Vandaag</h2>';
        } elseif($date == 'none') {
            $html = '';
        } elseif($date !== 'week') {
            $html = '<h2>Week van ' . $date .'</h2>';
        } else {
            $html = '<h2>Deze week</h2>';
        }

        $html .= '<table class="table" id="' . $date . '">';
        $html .= '<tr><th>Naam</th><th>Start</th><th>Einde</th><th>Vervanger</th><th>Reden</th>';
        foreach($InputCalendarItems as $CalendarItem){
            $replacement = $this->replacementToName($CalendarItem["CalendarItem"]["replacement_id"], $employees);
            $html .= '<tr id="'. $CalendarItem["CalendarItem"]["id"] . '"><td class="name"><a href="'. $this->base .'/employees/view/' . $CalendarItem["Employee"]["id"] . '">' .  $CalendarItem["Employee"]["name"] . ' '  . $CalendarItem["Employee"]["surname"]  . '</a></td>';
            $html .= '<td class="start">' . $CalendarItem["CalendarItem"]["start_date"] . ' ' . $CalendarItem["CalendarItem"]["start_time"] .'</td>';
            $html .= '<td class="end">' . $CalendarItem["CalendarItem"]["end_date"] . ' ' . $CalendarItem["CalendarItem"]["end_time"] .'</td>';
            $html .= '<td class="replacement"><a href="' . $this->base . '/employees/view/' . $replacement["Employee"]["id"] . '">' . $replacement["Employee"]["name"] .' '. $replacement["Employee"]["surname"] .'</a></td>';
            $html .= '<td class="reason">' . $CalendarItem["CalendarItemType"]["name"] . ' ' .$CalendarItem["CalendarItem"]["note"].'</td>';
        }
        $html .= '</table>';
        $html .= '<div class="spacer"></div>';
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

    public function valueOfCalendarItem($startdate, $enddate, $starttime = 'DAY', $endtime = 'DAY'){

        $startdate = new DateTime(strtotime($startdate));

        return $startdate;

    }

}
