

<div class="row">
    <div class="col-md-8">
        <div class="well">
            <h2><?php echo $user["User"]["name"] . ' ' . $user["User"]["surname"]?></h2>
            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li><?php echo $user["User"]["email"];?></li>
                        <li><?php echo $user["User"]["telephone"];?></li>
                        <li><?php echo $user["User"]["group"];?></li>
                    </ul>
                </div>
                <div class="col-md-6">
                <?php echo $user["User"]["note"];?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<table class="table">
    <tr><th width="15%">Start</th><th width="15%">Stop</th><th width="15%">Vervanger</th><th width="55%">Notitie</th><th></th></tr>
<?php
$verlofcollectie = $user["Verlof"];
foreach($verlofcollectie as $verlof){
    $verlofdate = date('d-m-Y', strtotime($verlof["start"]));
    $date = date('d-m-Y');
    if($verlofdate == $date){
        foreach($users as $user){
            if($user["User"]["id"] == $verlof["replacement_id"]){
                $replacement = '<a href="'. $this->base .'/users/view/'. $verlof["replacement_id"] .'">' . $user["User"]["name"] . ' ' . $user["User"]["surname"] . ' (' . $user["User"]["group"] . ')</a>';
            }
        }
        $struct = array('<tr><td>', '</td><td>', '</td></tr>');
        echo $struct[0] . date('d-m-Y H:i', strtotime($verlof["start"])) . $struct[1] . date('d-m-Y H:i', strtotime($verlof["end"])) . $struct[1] . $replacement . $struct[1] . $verlof["note"] .  '<td><span class="glyphicon glyphicon-chevron-right"></span>' . $struct[2];
    }
}


?>
</table>