<?php
class RolesController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('');
    }
    public function check(){
        $employees = $this->Role->find('all');
        var_dump($employees);
    }
}