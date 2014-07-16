<?php
class CalendarDay extends AppModel{
    public $belongsTo = array('RequestToCalendarDays', 'AuthItem', 'Employee', 'Replacement' => array('className' => 'Employee', 'type' => 'INNER', 'foreignKey' => 'replacement_id'));
}