<?php
class CalendarItem extends AppModel {
   public $belongsTo = array(
        'Employee' => array(
            'type' => 'INNER'
        ),
       'CalendarItemType'
       );

    public $validate = array(
        'start_date' => array(
            'required' => array(
                'rule' => array('date', 'ymd'),
                'allowEmpty' => false,
                'message' => 'Een begindatum is verplicht'
            )
        ));

}