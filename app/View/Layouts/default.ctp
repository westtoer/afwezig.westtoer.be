<!DOCTYPE html>
<html>
<head>
    <?php
    echo $this->Html->css('bootstrap');

    echo $this->Html->css('custom');
    echo $this->Html->css('paradigm/horizontal-tables');
    ?>
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css">
</head>
<body onload="onbodyload()">
<header>
    <div class="container">
        <div class="pull-left">
            <a href="<?php echo $this->base;?>" class="title"><h1 >Westtoer Op Verlof</h1></a>
        </div>
        <div class="pull-right">
            <ul class="menu">
                <?php
                if($this->Session->read('Auth.User.role') == 'admin' or  $this->Session->read('Auth.User.role') == 'steward'){
                   echo '<li class="menuitem">' . $this->Html->link('Verlof Goedkeuren', array('controller' => 'verlofs', 'action' => 'allow')) . '</li>';
                }
                echo '<li class="menuitem">' . $this->Html->link('Uitloggen', array('controller' => 'users', 'action' => 'logout')) . '</li>';
                ;?>
            </ul>
        </div></div>
</header>
<div class="container">
    <?php echo $this->Session->flash(); ?>

    <?php echo $this->fetch('content'); ?>

    <?php
    if($this->Session->read('Auth.User.role') == 'admin' and isset($this->request->query['sql'])){
        if($this->request->query['sql'] == true){
        echo $this->element('sql_dump');};
    }
    ?>
</div>
<?php echo $this->Html->script('bootstrap');?>
</body>
</html>