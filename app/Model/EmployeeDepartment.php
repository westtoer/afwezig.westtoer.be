<?php
App::uses('AppModel', 'Model');
class EmployeeDepartment extends AppModel {
    public $hasMany = array('Employees');
}