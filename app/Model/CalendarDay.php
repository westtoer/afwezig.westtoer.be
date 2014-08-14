<?php
class CalendarDay extends AppModel{
    public $belongsTo = array(
        'CalendarItemType',
        'Employee',
        'Replacement' => array('className' => 'Employee', 'type' => 'INNER', 'foreignKey' => 'replacement_id'),

    );
    public $hasMany = array(
        'RequestToCalendarDay'
    );

}