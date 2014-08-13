<?php
class AuthItem extends AppModel{
    public $hasOne = array('Request', 'Supervisor' => array(
        'type' => 'INNER',
        'className' => 'Employee',
        'foreignKey' => 'supervisor_id'
    ));
}