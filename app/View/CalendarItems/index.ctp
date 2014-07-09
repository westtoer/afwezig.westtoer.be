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
<div class="grip">
    <div id="button">Sluiten</div>
    <div class="closable">
        <hr />
        <div class="row">
            <div class="col-md-4">
                <?php echo $this->element('add-calendaritem'); ?>
            </div>
            <div class="col-md-8">
                <div class="well">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="<?php echo $this->base . '/?user=' . $this->Session->read('Auth.Employee.Employee.id');?>" class="btn btn-primary">Mijn Aanvragen</a>
                        </div>
                        <div class="col-md-10 right">
                            <input type="week" class="form-control tripled inline" onChange="update()" id="weekselect">
                            <select class="form-control tripled inline" onChange="update()" id="userselect">
                                <?php
                                    $x = $this->CalendarItem->selectorAllEmployees($employees, 'html', 1);
                                    foreach($x as $employeeOption){
                                        echo $employeeOption;
                                    }
                                ;?>
                            </select>
                            </select>
                            <select class="form-control tripled inline" onChange="update()" id="groupselect">
                               <?php
                                   $x = $this->CalendarItem->selectorAllEmployeeDepartments($employeeDepartments);
                                   foreach($x as $typeOption){
                                       echo $typeOption;
                                   }
                               ;?>
                            </select>
                        </div>


                    </div>
                </div>
                <div class="well">
                    <h3 class="first">Algemene feestdagen</h3>
                    <?php if(!empty($CalendarItemsGlobal)){echo $this->CalendarItem->globalCalendarItems($CalendarItemsGlobal);};?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Iterative blocks-->
<?php if(!empty($CalendarItemsToday)){echo $this->CalendarItem->tableCalendarItems($CalendarItemsToday, 'day', $employees);};?>
<?php
    if(!empty($CalendarItemsWeek)){
        echo $this->CalendarItem->tableCalendarItems($CalendarItemsWeek, 'week', $employees);
    }
;?>




<?php echo $this->Html->script('groupselect');?>
<?php echo $this->Html->script('weekcalc');?>
<script>
    $("#button").click(function(){
        if($(this).html() == "Open Controlepaneel"){
            $(this).html("Sluiten");
        }
        else{
            $(this).html("Open Controlepaneel");
        }
        $(".closable").slideToggle();
    });
</script>
