<?php
App::uses('AppModel', 'Model');
class Employee extends AppModel{
    public $hasMany = array('User');
    public $belongsTo = array('Role', 'EmployeeDepartment');
}