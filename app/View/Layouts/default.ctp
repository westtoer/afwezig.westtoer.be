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
    <?php echo $this->Html->script('bootstrap');?>
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
                if($this->Session->read('Auth.Role.allow') == 'true'){
                   echo '<li class="menuitem">' . $this->Html->link('Verlof Goedkeuren', array('controller' => 'Requests', 'action' => 'index')) . '</li>';
                }
                echo '<li class="menuitem">' . $this->Html->link('Uitloggen', array('controller' => 'users', 'action' => 'logout')) . '</li>';
                ;?>
            </ul>
        </div></div>
</header>
<div class="container">
    <?php
    if($this->Session->check('Message.flash')):?>
    <div class="alert alert-info alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <?php echo $this->Session->flash(); ?>
    </div>


    <?php endif;?>

    <?php echo $this->fetch('content'); ?>

    <?php
    if(isset($this->request->query['sql'])){
        if($this->request->query['sql'] == true){
        echo $this->element('sql_dump');};
    }
    ?>
    <hr />
    <?php echo $this->element('footer');?>
</div>
<?php echo $this->Html->script('bootstrap');?>
</body>
</html>