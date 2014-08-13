<?php
class CalendarItemType extends AppModel{
    public $hasOne = array('CalendarItemTypes' => array('className' => 'CalendarItemType', 'type' => 'INNER', 'foreignKey' => 'id'));
}