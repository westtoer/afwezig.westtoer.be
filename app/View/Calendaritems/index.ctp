<?php
    if(isset($_GET["range"])){
         $range = explode(';', $_GET["range"]);
         $onload[] = 'document.getElementById(\'weekselect\').value = \'' . $range[2] . '\';';
    }
    if(isset($_GET["user"])){
        $onload[] = 'document.getElementById(\'userselect\').value = \'' . $_GET["user"] . '\';';
    }
    if(isset($_GET["group"])){
        $onload[] = 'document.getElementById(\'groupselect\').value = \'' . $_GET["group"] . '\';';
    }
;?>
<script>
    function onbodyload(){
        <?php foreach($onload as $item){
            echo $item;
        };?>
    }
</script>

<div class="row">
    <div class="col-md-4">
        <?php echo $this->element('add-verlof'); ?>
    </div>
    <div class="col-md-8">
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <a href="<?php echo $this->base . '/?user=' . $this->Session->read('Auth.User.id');?>" class="btn btn-primary">Mijn Verlof</a>
                </div>
                <div class="col-md-10 right">
                    <input type="week" class="form-control tripled inline" onChange="update()" id="weekselect">
                    <select class="form-control tripled inline" onChange="update()" id="userselect">
                        <option value="0">Gebruiker</option>
                        <?php
                        foreach($users as $user){
                            $option .= '<option value="' .  $user["User"]["id"] . '">' . $user["User"]["name"] .   $user["User"]["surname"] . ' ('. $user["User"]["group"] .')</option>';
                        }
                        echo $option;
                        unset($option);
                        ;?>
                    </select>
                    </select>
                    <select class="form-control tripled inline" onChange="update()" id="groupselect">
                        <option value="0">Groep</option>
                        <?php
                        foreach($groups as $group){
                            $option .= '<option>' . $group["User"]["group"] . '</option>';
                        }
                        echo $option;
                        unset($option);
                        ;?>
                    </select>
                </div>


            </div>
        </div>
        <div class="well">Algemene Feestdagen</div>
    </div>
</div>
<!-- Iterative blocks-->
<h2>Vandaag</h2>
<table class="table">
    <tr><th>Naam</th><th>Dienst</th><th>Vertrek</th><th>Terugkeer</th><th>Contactpersoon</th><th>Notitie</th><th>&nbsp;</th></tr>
<?php

    foreach($verlofcollectie as $verlof){
        $verlofdate = date('d-m-Y', strtotime($verlof["Verlof"]["start"]));
        $date = date('d-m-Y');
        if($verlofdate == $date){
            foreach($users as $user){
                if($user["User"]["id"] == $verlof["Verlof"]["replacement_id"]){
                    $replacement = '<a href="'. $this->base .'/users/view/'. $verlof["Verlof"]["replacement_id"] .'">' . $user["User"]["name"] . ' ' . $user["User"]["surname"] . ' (' . $user["User"]["group"] . ')</a>';
                }
            }
            $struct = array('<tr><td>', '</td><td>', '</td></tr>');
            echo $struct[0] . $verlof["User"]["name"] . ' ' . $verlof["User"]["surname"] . $struct[1] . $verlof["User"]["group"] . $struct[1] . date('d-m-Y H:i', strtotime($verlof["Verlof"]["start"])) . $struct[1] . date('d-m-Y H:i', strtotime($verlof["Verlof"]["end"])) . $struct[1] . $replacement . $struct[1] . $verlof["Verlof"]["note"] .  '<td><span class="glyphicon glyphicon-chevron-right"></span>' . $struct[2];
        }
    }


?>
</table>

<h2>Deze week</h2>
<table class="table">
    <tr><th>Naam</th><th>Dienst</th><th>Vertrek</th><th>Terugkeer</th><th>Contactpersoon</th><th>Notitie</th><th>&nbsp;</th></tr>
    <?php

    foreach($verlofcollectie as $verlof){
        $verlofstart = date('d-m-Y', strtotime($verlof["Verlof"]["start"]));
        $verlofend = date('d-m-Y', strtotime($verlof["Verlof"]["end"]));
        $startdate = date('d-m-Y', strtotime(date('d-m-Y') . '+ 1 day'));
        $enddate = date('d-m-Y', strtotime(date('d-m-Y') . '+ 6 day'));
        if($startdate <= $verlofstart && $verlofend >= $enddate){
            foreach($users as $user){
                if($user["User"]["id"] == $verlof["Verlof"]["replacement_id"]){
                    $replacement = '<a href="'. $this->base .'/users/view/'. $verlof["Verlof"]["replacement_id"] .'">' . $user["User"]["name"] . ' ' . $user["User"]["surname"] . ' (' . $user["User"]["group"] . ')</a>';
                }
            }
            $struct = array('<tr><td>', '</td><td>', '</td></tr>');
            echo $struct[0] . $verlof["User"]["name"] . ' ' . $verlof["User"]["surname"] . $struct[1] . $verlof["User"]["group"] . $struct[1] . date('d-m-Y H:i', strtotime($verlof["Verlof"]["start"])) . $struct[1] . date('d-m-Y H:i', strtotime($verlof["Verlof"]["end"])) . $struct[1] . $replacement . $struct[1] . $verlof["Verlof"]["note"] .  '<td><span class="glyphicon glyphicon-chevron-right"></span>' . $struct[2];
        }
    }


    ?>
</table>

<?php echo $this->Html->script('groupselect');?>
<?php echo $this->Html->script('weekcalc');?>
<script>

</script>