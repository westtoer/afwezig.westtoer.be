<?php
App::uses('AppModel', 'Model');
class Role extends AppModel {
    public $hasMany = array('Employees');

}