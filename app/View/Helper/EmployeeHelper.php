<?php
class EmployeeHelper extends AppHelper {
    public function iterateOverEmployees($employees){
        $alphabet = range('a', 'z');
        $employeesSorted = '';
        foreach($alphabet as $letter){
            foreach($employees as $employee){
                if(strtolower(substr($employee["Employee"]["name"],0,1)) == $letter){
                    $employeesSorted[$letter][] = $employee;
                }
            }
        }

        $html = $this->tableAlphabethicalEmployees($employeesSorted);
        return $html;
    }

    private function tableAlphabethicalEmployees($employeesSorted){
        $alphabet = range('a', 'z');
        $html = '<div class="row">';
        foreach($alphabet as $letter){
            if(!empty($employeesSorted[strtolower($letter)])){
                $html .= '<div class="col-md-3">';
                $html .= '<h2 id="' . strtolower($letter) .'">' . strtoupper($letter) . '</h2>';
                $html .= '<ul class="employees">';
                foreach($employeesSorted[strtolower($letter)] as $employee){
                    $html .= '<li><a href="'. $this->base . '/employees/view/' .$employee["Employee"]["id"] . '">' . $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '</a></li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        return $html;
    }
    public function selectorAllEmployees($employees, $datatype = 'html', $placement = 0){
        //Give a standard null option
        if($datatype == 'html'){
            if($placement== 1){
                $employeesOptions[] = '<option value="4">Gebruiker</option>';
            } else {
                $employeesOptions[] = '<option value="4">Kies een vervanger</option>';
            }
            //Fill with all Employees
            foreach($employees as $employee){
                $employeesOptions[] = '<option value="' .$employee["Employee"]["id"] . '">' . $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '</option>';
            }
        } elseif($datatype == 'array'){
            //Give a standard null option
            $employeesOptions[] = array('name' => 'Kies uw vervanger (optioneel)', 'value' => 4);
            //Fill with all Employees
            foreach($employees as $employee){
                $employeesOptions[] = array('name' => $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"],
                    'value' => $employee["Employee"]["id"]);
            }
        }

        return $employeesOptions;
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
}