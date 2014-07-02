<?php
App::uses('AppModel', 'Model');
class Acl extends AppModel {
    public $belongsTo = array(
        'User' => array(
            'type' => 'INNER'
        )
    );
}