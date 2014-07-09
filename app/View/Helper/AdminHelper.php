<?php
class AdminHelper extends AppHelper {
    public function tableUsers($users){
        $html = '<table class="table">';
        $html .= '<tr><th>UiTID</th><th>Gekoppelt aan</th><th>Status</th><th>Acties</th></tr><tr>';
        foreach($users as $user){
            $html .= '<td>' . $user["User"]["uitid"] . '</td>';
            $html .= '<td>' . $user["Employee"]["name"] . ' ' . $user["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $this->checkIntegrityStatus($user['User']["status"], $user["Employee"]["linked"]) . '</td>';
            $html .= '<td>' . '<a href="' . $this->base . '/users/unlink/' . $user["Employee"]["id"] .'">Unlink</a>  |  <a href="' . $this->base . '/employees/view/' . $user["Employee"]["id"] .'">Profiel</a></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function tableCalendarItems($CalendarItems, $employees){
        $replacement = $this->replacementToName($CalendarItem["CalendarItem"]["replacement_id"], $employees);
        $html = '<table class="table">';
        $html .= '<tr><th>Naam</th><th>Start</th><th>Einde</th><th>Type Verlof</th><th>Verlofdagen over</th><th>Vervanger</th><th>Acties</th></tr><tr>';
        foreach($CalendarItems as $CalendarItem){
            $html .= '<td>' . $CalendarItem["Employee"]["name"] . ' ' . $CalendarItem["Employee"]["surname"] . '</td>';
            $html .= '<td>' . $CalendarItem["CalendarItem"]["start_date"] . ' ' . $CalendarItem["CalendarItem"]["start_time"] . '</td>';
            $html .= '<td>' . $CalendarItem["CalendarItem"]["end_date"] . ' ' . $CalendarItem["CalendarItem"]["end_time"] . '</td>';
            $html .= '<td>' . $CalendarItem["CalendarItemType"]["name"] . '</td>';
            $html .= '<td>' . $CalendarItem["Employee"]["daysleft"] . '</td>';
            $html .= '<td><a href="' . $this->base . '/employees/view/' . $CalendarItem["CalendarItem"]["replacement_id"] . '">' . $replacement["Employee"]["name"] . ' ' . $replacement["Employee"]["surname"] . '</a></td>';
            $html .= '<td><a href="' . $this->base . '/CalendarItems/allow/' . $CalendarItem["CalendarItem"]["id"] .'">Goedkeuren</a>  |  <a href="' . $this->base . '/CalendarItems/deny/' . $CalendarItem["CalendarItem"]["id"] . '">Weigeren</a>';
        }
        $html .= '</table>';
        return $html;
    }

    private function replacementToName($replacementId, $employees){
        do {
            foreach($employees as $possiblereplacement){
                if($replacementId == $possiblereplacement['Employee']['id']){
                    $replacement = $possiblereplacement;
                };
            };
        } while(empty($replacement));
        return $replacement;
    }

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