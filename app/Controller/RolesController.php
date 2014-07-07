<?php
class RolesController extends AppController {
    public function check(){
        $employees = $this->Role->find('all');
        var_dump($employees);
    }
}