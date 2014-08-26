<?php
class CalendarDay extends AppModel{
    public $belongsTo = array(
        'CalendarItemType',
        'Employee',
        'Replacement' => array('className' => 'Employee', 'foreignKey' => 'replacement_id'),

    );
    public $hasMany = array(
        'RequestToCalendarDay'
    );

}