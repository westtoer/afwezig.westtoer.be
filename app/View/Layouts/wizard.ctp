<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    echo $this->Html->css('bootstrap');
    echo $this->Html->css('filtergrid');
    echo $this->Html->css('custom');
    echo $this->Html->css('paradigm/horizontal-tables');
    ?>
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
    <?php echo $this->Html->script('bootstrap');?>
    <?php echo $this->Html->script('tablefilter.js');?>
</head>
<body>

<header>
    <div class="container">
        <div class="pull-left"><a href="http://intranet.westtoer.be/" class="title">Westtoer</a></div>
        <div class="pull-right">
            <ul class="menu">
                <li class="menuitem"><a href="http://intranet.westtoer.be">Intranet</li>
                <li class="menuitem"><a href="http://afwezig.westtoer.be">Afwezig</li>
            </ul>
        </div>
</header>


<div class="container">
    <div class="well"><a href="<?php echo $this->base;?>/admin/cancelEndOfYear">Wizard annuleren</a></div>
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