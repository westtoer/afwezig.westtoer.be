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


    public function selectorAllEmployees($employees){
        //Give a standard null option
        $employeesOptions[] = array('name' => 'Kies uw vervanger (optioneel)', 'value' => 0);
        //Fill with all Employees
        foreach($employees as $employee){
            $employeesOptions[] = array('name' => $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"],
                'value' => $employee["Employee"]["id"]);
        }

        return $employeesOptions;
    }

    public function selectorAllTypes($types){
        //Give a standard null option
        $typesOptions[] = array('name' => 'Kies uw type afwezigheid', 'value' => 0);
        //Fill with all Calendar Item Types
        foreach($types as $type){
            $typesOptions[] = array('name' => $type["CalendarItemType"]["code"] . '  -  '. $type["CalendarItemType"]["name"],
                'value' => $type["CalendarItemType"]["id"]);

        }

        return $typesOptions;
    }
}
