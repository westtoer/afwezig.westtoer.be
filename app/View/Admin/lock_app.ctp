<?php if($link == 'open'){
    $link = array('open', 'Ontgrendel Afwezig');
    $color = 'success';
} else {
    $link = array('close', 'Sluit de applicatie');
    $color = 'warning';
}
?><center>
    <a href="<?php echo $this->base;?>/admin/lockApp?action=<?php echo $link[0];?>" class="btn btn-<?php echo $color;?> btn-lg"><?php echo $link[1];?></a>
</center>