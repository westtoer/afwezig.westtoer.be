<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php foreach($registrations as $registree):?>
            <div class="row">
                <div class="col-md-12">
                    <div class="well flat">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="first"><?php echo $registree["Employee"]["name"] . ' ' . $registree["Employee"]["surname"];?><small> is geregistreerd</small></h3>
                                <p>Iemand heeft zich aangemeld als <b><?php echo $registree["Employee"]["name"] . ' ' . $registree["Employee"]["surname"];?></b> met het e-mailadres <b><?php echo $registree["User"]["email"];?></b></p>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo $this->base;?>/users/approve/<?php echo $registree["User"]["id"]?>" class="btn btn-success fullwidth spaced">Goedkeuren</a>
                                <a href="<?php echo $this->base;?>/users/deny/<?php echo $registree["User"]["id"]?>" class="btn btn-danger fullwidth spaced">Weigeren</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>