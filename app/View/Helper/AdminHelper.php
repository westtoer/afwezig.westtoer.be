<?php
class AdminHelper extends AppHelper {
    public function tableUsers($users, $type){
        $html = '<table class="table">';
        $html .= '<tr><th>UiTID</th><th>Gekoppelt aan</th><th>Status</th><th>Acties</th></tr><tr>';
        foreach($users as $user){
            $html .= '<td>' . $user["User"]["uitid"] . '</td>';
            $html .= '<td>' . $user["Employee"]["name"] . ' ' . $user["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $this->checkIntegrityStatus($user['User']["status"], $user["Employee"]["linked"]) . '</td>';
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

    private function checkIntegrityStatus($status, $linked){
        $x = '';
        if($status == 'active'){
            if($linked == 1){
                $x = 'Actief';
            } else{
                $x = 'Data mismatch, manueel overschreven';
            }
        } elseif($status == 'requested'){
            if($linked == 0){
                $x = 'Aangevraagd';
            } else{
                $x = 'Data mismatch, manueel overschreven';
            }
        } elseif($status == 'denied'){
            if($linked == 0){
                $x = 'Geweigerd';
            } else{
                $x = 'Data mismatch, manueel overschreven';
            }
        }
        return $x;
    }

    private function actionsBuilder(){

    }
}