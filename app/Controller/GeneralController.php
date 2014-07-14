<?php
App::uses('AppController', 'Controller');
class GeneralController extends AppController{

    public $helpers = array('Employee');
    public $uses = array('Employee', 'EmployeeDepartment');
    public function index(){
        $this->set('employees', $this->Employee->find('all'));
        $this->set('employeeDepartments', $this->EmployeeDepartment->find('all'));
    }
}