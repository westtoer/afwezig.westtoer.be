<?php
App::uses('AppModel', 'Model');
class Employee extends AppModel{
    public $hasMany = array('CalendarItem', 'User');
    public $belongsTo = array('Role', 'EmployeeDepartment');
}