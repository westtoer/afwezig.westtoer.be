<?php
class AdminHelper extends AppHelper {

    public $helpers = array('Form', 'CalendarItemType');

    public function tableUsers($users, $type){
        $html = '<table class="table">';
        $html .= '<tr><th>UiTID</th><th>Gekoppeld aan</th><th>Status</th><th>Acties</th></tr><tr>';
        foreach($users as $user){
            $html .= '<td>' . $user["User"]["email"] . '</td>';
            $html .= '<td>' . $user["Employee"]["name"] . ' ' . $user["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $this->checkIntegrityStatus($user['User']["status"]) . '</td>';
            if($type == 'active'){
                $html .= '<td>' . '<a href="' . $this->base . '/users/unlink/' . $user["User"]["id"] .'">Unlink</a>  |  <a href="' . $this->base . '/employees/view/' . $user["Employee"]["id"] .'">Profiel</a></td></tr>';
            } else if ($type == 'pending'){
                $html .= '<td>' . '<a href="' . $this->base . '/users/approve/' . $user["User"]["id"] .'">Goedkeuren</a> | '   . '<a href="' . $this->base . '/users/deny/' . $user["User"]["id"] .'">Weigeren</a></td></tr>';
            } else if ($type == 'denied'){
                $html .= '<td></td></tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }

    public function tableRequests($Requests, $actions = 1){
        $html = '<table class="table">';
        $html .= '<tr><th>Naam</th><th>Start</th><th>Einde</th><th>Type Verlof</th><th>Verlofdagen over</th>';
        if($actions == 1){
            $html .= '<th>Acties</th><th>Vervanger</th>';
        }
        $html .= '</tr><tr>';
        foreach($Requests as $Request){
            $html .= '<td>' . $Request["Employee"]["name"] . ' ' . $Request["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $Request["Request"]["start_date"] . ' ' . $Request["Request"]["start_time"] . '</td>';
            $html .= '<td>' . $Request["Request"]["end_date"] . ' ' . $Request["Request"]["end_time"] . '</td>';
            $html .= '<td>' . $Request["CalendarItemType"]["name"] . '</td>';
            $html .= '<td>' . $Request["Employee"]["daysleft"] . '</td>';
            if($actions == 1){
                $html .= '<td><a href="' . $this->base . '/employees/view/' . $Request["Request"]["replacement_id"] . '">' . $Request["Replacement"]["name"] . ' ' . $Request["Replacement"]["surname"] . '</a></td>';
                $html .= '<td><a href="' . $this->base . '/Requests/allow/' . $Request["Request"]["id"] .'">Goedkeuren</a>  |  <a href="' . $this->base . '/Requests/deny/' . $Request["Request"]["id"] . '">Weigeren</a></td></tr>';
            } else {
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }

    public function tableDinnerCheques($employees){
        $html = '<table class="table">';
        $html .= '<tr><th>Naam</th><th>Maaltijdcheques</th></tr>';
        foreach($employees as $employee){
            $html .= '<tr><td>' . $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $employee["Employee"]["dinner_cheques"] . '</td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /*private function replacementToName($replacementId, $employees){
        do {
            foreach($employees as $possiblereplacement){
                if($replacementId == $possiblereplacement['Employee']['id']){
                    $replacement = $possiblereplacement;
                };
            };
        } while(empty($replacement));
        return $replacement;
    }*/

    private function checkIntegrityStatus($status){
        $x = '';
        if($status == 'active'){
                $x = 'Actief';
        } elseif($status == 'requested'){
                $x = 'Aangevraagd';
        } elseif($status == 'denied'){
                $x = 'Geweigerd';
        }
        return $x;
    }

    public function tableHolidays($holidays){
        $html = $this->Form->create('Request', array('url' => '/admin/endOfYear?step=7'));
        $html .= '<table class="table">';
        $html .= '<tr><th>Naam</th><th>Datum</th><th class="selectbox-td"><input type="checkbox" id="selectAll" onChange="changeState()"></th></tr>';
        foreach($holidays as $key => $holiday){
            $html .= '<tr>';
            $html .= '<td>' . $holiday["Request"]["name"] .'</td>';
            $html .= '<td>';
                if($holiday["Request"]["start_date"] == $holiday["Request"]["end_date"]){
                    $html .= $holiday["Request"]["start_date"];
                } else {
                    $html .= $holiday["Request"]["start_date"] . '  -  ' . $holiday["Request"]["end_date"];
                }
            $html .= '</td>';
            $html .= '<td class="selectbox-td"><input type="hidden" name="data[Request]['. $key .'][id]" value="' . $holiday["Request"]["id"] . '"><input type="checkbox" class="selectbox" name="data[Request][' . $key .'][request_copy]"></td>';
        }
        $html .= '</table>';
        $html .= $this->Form->submit("Feestdagen overzetten", array('class' => 'btn btn-primary fullwidth'));
        $html .= $this->Form->end();
        return $html;
    }

    public function tableCalendarItemTypes($calendarTypes){
        $html = $this->Form->create('CalendarItemType', array('url' => $this->here));
        $html .= '<table class="table">';
        $html .= '<tr><th width="200px">Naam</th><th>Code</th><th width="20px">Zichtbaar</th><th width="20px">Maaltijdcheque</th><th>Schaubroek Code</th><th>Schaubroek aard</th><th>Schaubroek Extensie</th></tr>';
        foreach($calendarTypes as $key => $calendarType){
            $html .= '<tr><td><input type="text" name="data['.$key.'][CalendarItemType][name]" value="' . $calendarType["CalendarItemType"]["name"] .'" class="form-control"></td>';
            $html .= '<td><input type="text" name="data['.$key.'][CalendarItemType][code]" value="' . $calendarType["CalendarItemType"]["code"] .'" class="form-control"></td>';
            $html .= '<td width="20px"><select name="data['.$key.'][CalendarItemType][user_allowed]" class="form-control">'. $this->currentlySelected($calendarType["CalendarItemType"]["user_allowed"]) .'</select></td>';
            $html .= '<td><select name="data['.$key.'][CalendarItemType][dinner_cheque]" class="form-control">'. $this->currentlySelected($calendarType["CalendarItemType"]["dinner_cheque"]) .'</select></td>';
            $html .= '<td><input type="text" name="data['.$key.'][CalendarItemType][code_schaubroek]" value="' . $calendarType["CalendarItemType"]["code_schaubroek"] .'" class="form-control"></td>';
            $html .= '<td><input type="text" name="data['.$key.'][CalendarItemType][aard_schaubroek]" value="' . $calendarType["CalendarItemType"]["aard_schaubroek"] .'" class="form-control"></td>';
            $html .= '<input type="hidden" name="data['.$key.'][CalendarItemType][id]" value="'.  $calendarType["CalendarItemType"]["id"]  .'">';
            $html .= '<td><input type="text" name="data['.$key.'][CalendarItemType][ext_schaubroek]" value="' . $calendarType["CalendarItemType"]["ext_schaubroek"] .'" class="form-control"></td></tr>';
        }
        $html .= '</table>';
        $html .= $this->Form->submit("Dagcodes opslaan", array('class' => 'btn btn-primary fullwidth'));
        $html .= $this->Form->end();
        return $html;
    }

    private function currentlySelected($value){
        if($value == true){
            $html ='<option value="1">Ja</option><option value="0">Nee</option>';
        } elseif($value == false) {
            $html ='<option value="0">Nee</option><option value="1">Ja</option>';
        }
        return $html;
    }

    public function webview($data){

    $html = '';
        foreach($data as $employee => $dateObjects){
            $html .= '<h3 class="first">' . $employee . '</h3>';
            foreach($dateObjects as $dateObject => $type){
                $dates[explode('/', $dateObject)[0]][explode('/', $dateObject)[1]] = $type;
            }
            $html .= '<table class="table table-bordered overview"><tr>';

            foreach($dates as $date => $time){
                $html .= '<th colspan=2 class="date">' . explode('-',$date)[2] . '</th>';
            }
            $html .= '</tr><tr>';
            foreach($dates as $time){
                foreach($time as $timeblocks){
                    if(is_array($timeblocks)){
                        if($timeblocks[1] == "G"){
                            $html .= '<td></td>';
                        } else {
                            $html .= '<td>' . $timeblocks[1] . '</td>';
                        }
                    } else {
                        if($timeblocks == "G"){
                            $html .= '<td></td>';
                        } elseif($timeblocks == "ZA" or $timeblocks == "ZO"){
                            $html .= '<td class="black"></td>';
                        }else {
                            $html .= '<td>' . $timeblocks . '</td>';
                        }
                    }
                }
            }
            $html .= '</tr></table>';
        }


        return $html;

    }

    public function tableDepartments($departments){
        $html = '<table class="table">';
        $html .= '<tr><th>ID</th><th>Naam</th><th># werknemers</th><th>Acties</th></tr>';

        foreach($departments as $department){
            $html .= '<tr>';
            $html .= '<td id="id-' . $department["EmployeeDepartment"]["id"] .'">' . $department["EmployeeDepartment"]["id"] . '</td>';
            $html .= '<td id="name-' . $department["EmployeeDepartment"]["id"] .'">' . $department["EmployeeDepartment"]["name"] . '</td>';
            $html .= '<td>' . count($department["Employees"]) . '</td>';
            $html .= '<td id="action-'.$department["EmployeeDepartment"]["id"] .'"><a href="' .$this->here .'?id=' . $department["EmployeeDepartment"]["id"] . '&action=delete">Verwijder</a>  |  <a href="' . $this->here . '?id='. $department["EmployeeDepartment"]["id"].'&action=edit">Wijzig</a></td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    public function crudCalendarDays($calendarDays, $cit){
        $types = $this->CalendarItemType->selectorAllCalendarItemTypes($cit, 'mixed');
        $html = '<table class="table table-bordered">';
        $html .= '<tr><th width="200px">Dag</th><th>AM</th><th>PM</th>';

        foreach($calendarDays as $date =>$cd){
            $html .= '<tr>';
            $html .= '<td>' . $date .'</td>';
            foreach($cd as $hour => $desc){
                $html .= '<td>';
                $html .= '<select class="form-control" name="data[items][' . $date . '][' . $hour . '][' . $cd[$hour]["id"] . '][type]">';
                $html .= '<option value="' . $cd[$hour]["type_id"] . '">' .$cd[$hour]["name"]  .'</option>';
                foreach($types as $type){
                    if($type["id"] !== $cd[$hour]["type_id"]){
                        $html .= $type["html"];
                    }
                }
                $html .= '</select>';
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';


        return $html;
    }
}