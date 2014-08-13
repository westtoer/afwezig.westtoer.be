<?php
App::uses('AppModel', 'Model');
class Employee extends AppModel{
    public $hasMany = array('User', 'CalendarDay');
    public $belongsTo = array('Role', 'EmployeeDepartment', 'Supervisor' => array(
        'type' => 'LEFT',
        'className' => 'Employee',
        'foreignKey' => false,
        'conditions' => array('Employee.supervisor_id = Supervisor.internal_id')
    ));
}