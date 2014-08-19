<?php
class Stream extends AppModel{

    public $belongsTo = array('CalendarItemType',
        'Employee' => array(
            'type' => 'INNER',
            'className' => 'Employee',
            'foreignKey' => false,
            'conditions' => array('Stream.employee_id = Employee.internal_id')
        ));

}