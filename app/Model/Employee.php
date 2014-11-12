<?php
App::uses('AppModel', 'Model');
class Employee extends AppModel{
    public $hasMany = array('User', 'CalendarDay');
    public $hasOne = array(
        'EmployeeCount' => array(
            'foreignKey' => false,
            'conditions' => array('Employee.internal_id = EmployeeCount.employee_id')
        )
    );
    public $belongsTo = array('Role', 'EmployeeDepartment', 'Supervisor' => array(
        'type' => 'LEFT',
        'className' => 'Employee',
        'foreignKey' => false,
        'conditions' => array('Employee.supervisor_id = Supervisor.internal_id')
    ),

    );
}