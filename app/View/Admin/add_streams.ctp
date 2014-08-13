<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
    </div>
    <div class="col-md-9">
        <h1 class="first">Stramienregels opzetten</h1>
        <p>Om voltijdse werkschema aan te passen aan de reële situatie van een werknemer, kun je stramienregels aanmaken. Deze regels zorgen ervoor dat een werkschema wekelijks of twee-wekelijks automatisch wordt aangepast.</p>
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 right"><a href="#" onClick="addRule()">Voeg een nieuw stramien toe</a></div>
        </div>

        <div id="rules-wrapper">
            <?php echo $this->Form->create('Stream', array('url' => '/admin/addStreams'));?>
            <div id="rules-inner">

            </div>
            <?php echo $this->Form->submit('Sla stramienen op', array('class' => 'btn btn-primary fullwidth'));?>
            <?php echo $this->Form->end();?>
        </div>


        <?php $employeesSelector = $this->Employee->selectorAllEmployees($employees, 'html', 1);?>
        <?php $CITSelector = $this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes);?>
    </div>
</div>

<script>
    var amountOfRules = 1;

    function addRule(){

        var rule = '<div id="rule-' + amountOfRules +'"class="form-item-wrapper well ">' +
            '<div class="row">' +
            '<div class="col-md-2 formspaced-left">' +
            '<input type="number" class="form-control " placeholder="Elke nde van" name="data[' + amountOfRules + '][Stream][day_relative]">' +
            '</div>' +
            '<div class="col-md-2 formspaced-left formspaced-right">' +
            '<select class="form-control" name="data[' + amountOfRules + '][Stream][day_time]">' +
            '<option value="day">Gehele dag</option>'+
            '<option value="AM">Voormiddag</option>'+
            '<option value="PM">Namiddag</option>'+
            '</select>' +
            '</div>' +
            '<div class="col-md-4  formspaced-left formspaced-right">' +
            '<select class="form-control" name="data[' + amountOfRules + '][Stream][rule_type]"><option value="w">elke week</option><option value="ww">elke twee weken</option></select>' +
            '</div>' +
            '<div class="col-md-4 formspaced-right">' +
            '<select class="form-control" name="data[' + amountOfRules + '][Stream][employee_id]"><?php foreach($employeesSelector as $employee){ echo $employee;}?></select>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-6 formspaced-left">' +
            '<select class="form-control spaced" name="data[' + amountOfRules + '][Stream][calendar_item_type_id]"><?php foreach($CITSelector as $calendaritemtype){ echo $calendaritemtype;}?></select>' +
            '</div>' +
            '<div class="col-md-6 right formspaced-right">' +
            '<a class="btn btn-danger fullwidth spaced"href="#" onClick="removeRule(' + amountOfRules +')">Verwijder regel</a>' +
            '</div>' +
            '</div>'

        $("#rules-inner").append(rule);
        amountOfRules++;
    }

    function removeRule(rule){
        $('#rule-' + rule).remove();
    }
</script>