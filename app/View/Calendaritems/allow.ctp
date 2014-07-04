<?php if(sizeof($pending) > 0):?>
    <?php foreach($pending as $pendinggroup):?>
        <div class="row">
            <?php foreach($pendinggroup as $verlof): ;?>
                <?php if($verlof !== $pendinggroup["title"]):?>
                    <div class="col-md-6">
                        <div class="well flat">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="null"><?php echo $verlof["User"]["name"] . ' ' . $verlof["User"]["surname"] . ' (' . $verlof["User"]["group"] . ')';?></h3>
                                    <p>Heeft verlof aangevraagd vanaf <?php echo date('d-m-Y H:i', strtotime($verlof["Verlof"]["start"]));?> tot en met <?php echo date('d-m-Y H:i', strtotime($verlof["Verlof"]["end"]));?></p>
                                    <p></p>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo $this->base . '/verlofs/overlap/' . $verlof["Verlof"]["id"];?>" class="btn btn-primary fullwidth spaced">Bekijk overlap</a>
                                    <a href="<?php echo $this->base . '/verlofs/approved/' . $verlof["Verlof"]["id"];?>" class="btn btn-success fullwidth spaced">Accepteer</a>
                                    <a href="<?php echo $this->base . '/verlofs/delete/' . $verlof["Verlof"]["id"];?>" class="btn btn-danger fullwidth spaced">Weiger</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            <?php endforeach;?>
        </div>
    <?php endforeach;?>
<?php endif;?>

