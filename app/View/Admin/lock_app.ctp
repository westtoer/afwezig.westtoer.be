<?php if($link == 'open'){
    $link = array('open', 'Ontgrendel Afwezig');
} else {
    $link = array('close', 'Sluit de applicatie');
}
?><center>
    <a href="<?php echo $this->base;?>/admin/lockApp?action=<?php echo $link[0];?>" class="btn btn-primary btn-lg"><?php echo $link[1];?></a>
</center>