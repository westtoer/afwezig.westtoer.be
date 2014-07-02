<?php
App::uses('AppModel', 'Model');
class Verlof extends AppModel {
    public $belongsTo = array(
        'User' => array(
            'type' => 'INNER'
        )
    );
}