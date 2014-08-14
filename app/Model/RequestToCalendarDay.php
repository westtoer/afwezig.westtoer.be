<?php
class RequestToCalendarDay extends AppModel {
    public $belongsTo = array('CalendarDay','Request');
}