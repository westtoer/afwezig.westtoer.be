<?php
    Router::connect('/uitid/login', array('plugin' => 'Uitid', 'controller' => 'Uitid', 'action' => 'uitid'));
    Router::connect('/uitid/callback', array('plugin' => 'Uitid', 'controller' => 'Uitid', 'action' => 'callback'));
