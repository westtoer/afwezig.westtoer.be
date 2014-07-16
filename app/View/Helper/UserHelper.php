<?php
class UserHelper extends AppHelper{

    public function tableLinkedUsers($linkedUsers){
        $html = '<table class="table">';
        $html .= '<tr><th>UiTID</th><th>Email</th><th>Status</th><th>Acties</th></tr>';
        foreach($linkedUsers as $linkedUser){
            $html .= '<tr><td>' . $linkedUser["User"]["uitid"] . '</td>';
            $html .= '<td>' . $linkedUser["User"]["email"] . '</td>';
            $html .= '<td>' . $linkedUser["User"]["status"] . '</td>';
            $html .= '<td><a href="' . $this->base . '/users/unlink/' . $linkedUser["User"]["id"] .'">Unlink</td>';
        }
        $html .= '</table>';

        return $html;
    }
}