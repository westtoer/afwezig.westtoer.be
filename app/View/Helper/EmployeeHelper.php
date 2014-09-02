<?php
class EmployeeHelper extends AppHelper {

    public $helpers = array('Form');

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
            if($placement == 1){
                $employeesOptions[] = '<option value="-1">Gebruiker</option>';
            } elseif($placement == 2) {
                $employeesOptions[] = '<option value="0">voor</option>';
            } elseif($placement == 3) {
                $employeesOptions[] = '<option value="0">Gebruiker</option>';
            } elseif(is_string($placement)){
                $employeesOptions[] = '<option value="0">' . $placement .'</option>';
            } else {
                $employeesOptions[] = '<option value="4">Kies een vervanger</option>';
            }
            //Fill with all Employees
            foreach($employees as $employee){
                $employeesOptions[] = '<option value="' .$employee["Employee"]["internal_id"] . '">' . $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '</option>';
            }
        } elseif($datatype == 'array'){
            //Give a standard null option
            if($placement == 1){
                $employeesOptions[] = array('name' => 'Verantwoordelijke', 'value' => 0);
            }  elseif($placement == 3) {
                $employeesOptions[] = array('name' => 'Werknemer', 'value' => 0);
            } else {
                $employeesOptions[] = array('name' => 'Kies uw vervanger (optioneel)', 'value' => -1);
            }
            //Fill with all Employees
            if($placement != 3){
                foreach($employees as $employee){
                    if($employee["Employee"]["internal_id"] !== ''){
                        $employeesOptions[] = array('name' => $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"],
                            'value' => $employee["Employee"]["internal_id"]);
                    }
                }
            } else {
                foreach($employees as $employee){
                    if($employee["Employee"]["internal_id"] !== ''){
                        $employeesOptions[] = array('name' => $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"],
                            'value' => $employee["Employee"]["id"]);
                    }
                }
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

    public function tableEmployees($employees, $tableid = '1'){
        $html = '<table id="' . $tableid . '" class="table" >';
        $html .= '<tr><th width="20px"></th><th>Naam</th><th>3gram</th><th>Acties</th></tr>';
        foreach($employees as $employee){
            $html .= '<tr>';
            $html .= '<td><input class="employeesSelector" type="checkbox" id="Select' . $employee["Employee"]["id"].'" data-employee-id="'. $employee["Employee"]["id"] .'"></td>';
            $html .= '<td>' . $employee["Employee"]["name"] . ' ' .$employee["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $employee["Employee"]["3gram"] . '</td>';
            $html .= '<td><a href="' .$this->base .'/admin/editEmployee/' . $employee["Employee"]["id"] . '">Pas aan</a>  |  <a href="' .$this->base .'/Employees/view/' . $employee["Employee"]["id"] . '">Bekijk</a></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }



    public function tableEndOfYear($employees, $type = 'mutate', $tableid = "1", $chainstep){
        $html = $this->Form->create('Employee', array('url' => '/admin/endOfYear?step=' . $chainstep));
        $html .= '<table id="' . $tableid . '" class="table" >';
        $html .= '<tr><th>Naam</th><th>Halve dagen over dit jaar</th>';

        if($type == 'new'){
            $html .= '<th>Aantal halve dagen dit jaar</th></tr>';
            $html .= '<tr><td></td><td></td><td><input id="director" type="text" class="form-control" placeholder="Standaard aantal halve dagen" onChange="updateFields()"></td>';
        } else {
            $html .= '<th>Aangepast saldo halve dagen</th></tr>';
        }
        foreach($employees as $key => $employee){
            $html .= '<tr>';
            $html .= '<td>' . $employee["Employee"]["name"] . ' ' .$employee["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $employee["Employee"]["daysleft"] .'</td>';
            $html .= '<td width="200px">'. '<input name="data[Employee][' . $key .'][id]" value="' . $employee["Employee"]["id"] .'" id="Employee'. $key .'id" type="hidden">' . '<input name="data[Employee]['. $key .'][daysleft]" data-' . $key .'-daysleft="' . $employee["Employee"]["daysleft"] .'" class="form-control listener" id="Employee'. $key.'Daysleft">' .'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<script>var amountOfRows = ' . $key . ' + 1</script>';
        $html .= $this->Form->submit('Volgende', array('class' => 'btn btn-primary fullwidth'));
        $html .= $this->Form->end();
        return $html;
    }

}