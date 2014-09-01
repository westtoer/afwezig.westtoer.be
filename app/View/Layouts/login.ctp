<!DOCTYPE html>
<html>
<head>
    <title>Westtoer Afwezig - Login</title>
    <?php
    echo $this->Html->css('bootstrap');
    echo $this->Html->css('custom');
    echo $this->Html->css('spec/login');
    ?>
    <?php echo $this->Html->script('css3-mediaqueries')?>
</head>
<body>
<div class="container zi-top">
    <?php
    if($this->Session->check('Message.flash')):?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <?php echo $this->Session->flash(); ?>
        </div>


    <?php endif;?>

    <?php echo $this->fetch('content');?>
</div>
<div class="footer zi-bottom">
</div>
</body>
</html>