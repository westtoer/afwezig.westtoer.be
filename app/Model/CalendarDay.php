<?php
class CalendarDay extends AppModel{
    public $belongsTo = array('CalendarItemType', 'RequestToCalendarDays', 'AuthItem', 'Employee', 'Replacement' => array('className' => 'Employee', 'type' => 'INNER', 'foreignKey' => 'replacement_id'));

}