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
        <?php echo $this->element('add-calendaritem'); ?>
    </div>
    <div class="col-md-8">
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <a href="<?php echo $this->base . '/?user=' . $this->Session->read('Auth.User.id');?>" class="btn btn-primary">Mijn Aanvragen</a>
                </div>
                <div class="col-md-10 right">
                    <input type="week" class="form-control tripled inline" onChange="update()" id="weekselect">
                    <select class="form-control tripled inline" onChange="update()" id="userselect">
                        <option value="0">Gebruiker</option>
                        <?php
                        foreach($employees as $employee){
                            $option .= '<option value="' .  $employee["Employee"]["id"] . '">' . $employee["Employee"]["name"] .   $employee["Employee"]["surname"].'</option>';
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
<?php

echo $calendarhtml[0];



?>


    <?php


    echo $calendarhtml[1];



    ?>


<?php echo $this->Html->script('groupselect');?>
<?php echo $this->Html->script('weekcalc');?>
<script>

</script>