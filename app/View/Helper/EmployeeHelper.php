<?php
class EmployeeHelper extends AppHelper {
    public function iterateOverEmployees($employees){
        $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $employeesSorted = array();
        foreach($alphabet as $letter){
            foreach($employees as $employee){
                var_dump(substr($employee["Employee"]["name"],0));
                if(substr($employee["Employee"]["name"],0,1) == $letter){
                    $employeesSorted[$letter][] = $employee;
                }
            }
        }
        return $employeesSorted;
    }
}