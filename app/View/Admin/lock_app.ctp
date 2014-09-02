<div class="row">
    <div class="col-md-3"><?php echo $this->element('admin/base_admin_menu');?></div>
    <div class="col-md-9">
    <div class="alert alert-info">
        <p>Door de applicatie te sluiten, kunnen enkel administrators en mensen met de rol HR nog aanmelden op Afwezig. Dit kan je doen om bijvoorbeeld te zorgen dat mensen tijdelijk geen aanpassingen kunnen doen in de database</p>
    </div>
        <?php if($link == 'open'){
                $link = array('open', 'Ontgrendel Afwezig');
                $color = 'success';
            } else {
                $link = array('close', 'Sluit de applicatie');
                $color = 'warning';
            }
        ?>
        <center>
            <a href="<?php echo $this->base;?>/admin/lockApp?action=<?php echo $link[0];?>" class="btn btn-<?php echo $color;?> btn-lg"><?php echo $link[1];?></a>
        </center>
    </div>
</div>
