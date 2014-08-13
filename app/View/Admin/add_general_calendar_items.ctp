<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php echo $this->Form->create('Request', array('url' => array('controller' => 'admin', 'action' => 'addGeneralCalendarItems')));?>
        <div class="row">
            <div class="col-md-8">
                <?php echo $this->Request->tableRequests($requests, 1);?>
            </div>
            <div class="col-md-4">
                <div class="well flat">
                    <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Naam van de feestdag'));?>
                    <div class="row">
                        <div class="col-md-8 formspaced-left spaced">
                            <input class="form-control" name="data[Request][start_date]" type="text" placeholder="Start" value="" id="RequestStartDate">
                        </div>
                        <div class="col-md-4 formspaced-right spaced">
                            <?php echo $this->Form->input('start_time', array('label' => false, 'class' => 'form-control', 'options' => array(
                                array('name' => 'AM', 'value' => 'AM'),
                                array('name' => 'PM', 'value' => 'PM'),
                            )));?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 formspaced-left spaced">
                            <input class="form-control" name="data[Request][end_date]" type="text" placeholder="Einde" value="" id="RequestEndDate">
                        </div>
                        <div class="col-md-4 formspaced-right spaced">
                            <?php echo $this->Form->input('end_time',  array('label' => false, 'class' => 'form-control', 'options' => array(
                                array('name' => 'PM', 'value' => 'PM'),
                                array('name' => 'AM', 'value' => 'AM'),
                            )));?>
                        </div>
                    </div>
                    <?php echo $this->Form->submit('Verstuur', array('class' => 'btn btn-primary fullwidth'));?>
                    <?php echo $this->Form->end();?>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    $(function(){
        $.datepicker.setDefaults(
            $.extend($.datepicker.regional['nl'])
        );
        $('#RequestStartDate').datepicker({
            minDate: '-0',
            dateFormat: 'yy-mm-dd',
            closeText: 'Sluiten',
            prevText: '←',
            nextText: '→',
            currentText: 'Vandaag',
            monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
            monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
                'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
            dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
            dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
            dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
            weekHeader: 'Wk'
        });
    });
    $(function(){
        $.datepicker.setDefaults(
            $.extend($.datepicker.regional['nl'])
        );
        $('#RequestEndDate').datepicker({
            minDate: '-0',
            dateFormat: 'yy-mm-dd',
            closeText: 'Sluiten',
            prevText: '←',
            nextText: '→',
            currentText: 'Vandaag',
            monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
            monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
                'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
            dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
            dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
            dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
            weekHeader: 'Wk'
        });
    });
</script>
