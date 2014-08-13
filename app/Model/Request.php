<?php
class Request extends AppModel {
   public $belongsTo = array(
                                'Employee' => array(
                                    'type' => 'INNER'
                                ),
                                'Replacement' => array(
                                    'type' => 'INNER',
                                    'className' => 'Employee',
                                    'foreignKey' => false,
                                    'conditions' => array('Request.replacement_id = Replacement.internal_id')
                                ),
                                'CalendarItemType',
                                'AuthItem',
       );
   public $hasMany = array('RequestToCalendarDays');


}