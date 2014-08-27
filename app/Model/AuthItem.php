<?php
class AuthItem extends AppModel{
    public $hasOne = array('Request', 'Supervisor' => array(
        'type' => 'INNER',
        'className' => 'Employee',
        'foreignKey' => false,
        'conditions' => array('Employee.supervisor_id = Supervisor.internal_id')
    ));
}