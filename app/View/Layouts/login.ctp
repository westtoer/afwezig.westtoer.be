<!DOCTYPE html>
<html>
<head>
    <title></title>
    <?php
    echo $this->Html->css('bootstrap');
    echo $this->Html->css('custom');
    echo $this->Html->css('spec/login');
    ?>
</head>
<body>
<div class="container zi-top">
    <?php echo $this->fetch('content');?>
</div>
<div class="footer zi-bottom">
</div>
</body>
</html>