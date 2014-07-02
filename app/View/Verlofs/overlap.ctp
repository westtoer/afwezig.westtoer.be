
<div class="well">
    <div class="row">
        <div class="col-md-3">
            <select class="form-control" onChange="groupselect()" id="groupselect">
                <option value="0">Verfijn Op Groep</option>
                <option value="1">Reset</option>
                <?php
                    foreach($groups as $group){
                        $option .= '<option>' . $group["User"]["group"] . '</option>';
                    }
                echo $option;
                ;?>
            </select>
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-6">
                    <div class="well flat darker">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="null"><?php echo $searchquery["User"]["name"] . ' ' . $searchquery["User"]["surname"] . ' (' . $searchquery["User"]["group"] . ')';?></h3>
                                <p>Heeft verlof aangevraagd vanaf <?php echo date('d-m-Y H:i', strtotime($searchquery["Verlof"]["start"]));?> tot en met <?php echo date('d-m-Y H:i', strtotime($searchquery["Verlof"]["end"]));?></p>
                                <p></p>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo $this->base . '/verlofs/approved/' . $searchquery["Verlof"]["id"];?>" class="btn btn-success fullwidth spaced">Accepteer</a>
                                <a href="<?php echo $this->base . '/verlofs/delete/' . $searchquery["Verlof"]["id"];?>" class="btn btn-danger fullwidth spaced">Weiger</a>


                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>
<?php if(isset($output)):?>
    <?php foreach($output as $verlof):?>
        <div class="row">
                    <div class="col-md-6">
                        <div class="well flat">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="null"><?php echo $verlof["User"]["name"] . ' ' . $verlof["User"]["surname"] . ' (' . $verlof["User"]["group"] . ')';?></h3>
                                    <p>Heeft verlof aangevraagd vanaf <?php echo date('d-m-Y H:i', strtotime($verlof["Verlof"]["start"]));?> tot en met <?php echo date('d-m-Y H:i', strtotime($verlof["Verlof"]["end"]));?></p>
                                    <p></p>
                                </div>
                                <div class="col-md-4">
                                    <h3>
                                        <?php
                                        if($verlof["Verlof"]["allowed"] == 1){
                                            echo 'Dit verlof is goedgekeurd';
                                        } else {
                                            echo 'Dit verlof is nog niet goedgekeurd';
                                        }
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    <?php endforeach;?>
<?php endif;?>
<?php echo $this->Html->script('groupselect');?>
