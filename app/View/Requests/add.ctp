<div class="row">
    <div class="col-md-4">
        <div class="well flat">
            <h2 class="first">Verlof aanvragen</h2>
            <div id="error"></div>
            <?php echo $this->Form->create('Request');?>
            <?php echo $this->Form->hidden('employee_id', array('value' => 0));?>
            <?php echo $this->Form->hidden('auth_item_id', array('value' => 0));?>
            <?php echo $this->Form->hidden('timestamp', array('value' => 0));?>
            <div class="row">
                <div class="col-md-8 formspaced-left spaced">
                    <input class="form-control" name="data[Request][start_date]" type="text" placeholder="Start" value="" id="RequestStartDate" OnChange="onChangeBegin()">
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



            <?php echo $this->Form->input('calendar_item_type_id', array('label' => false, 'class' => 'form-control spaced', 'options' => array($this->Request->selectorAllTypes($types, 'array'))));?>
            <?php echo $this->Form->input('replacement_id', array('label' => false, 'class' => 'form-control spaced', 'options' => array($this->Employee->selectorAllEmployees($employees, 'array'))));?>

            <?php //echo $this->Form->submit('Verstuur', array('class' => 'btn btn-primary fullwidth', 'onClick' => 'initiated()', 'id' => 'RequestSubmitButton'));?>
            <?php echo $this->Form->end();?>
            <button id="RequestSubmitButton" class="btn btn-primary fullwidth" onClick="initiated()">Verstuur</button>

        </div>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Sluit</span></button>
            <strong>Info!</strong> Om een verlofaanvraag te annuleren, overschrijf je hem met een nieuwe aanvraag. Kies dan in plaats van verlof (of een andere categorie) voor Werk.
            Om meer te lezen hierover, ga je naar het <a href="">intranet</a>
        </div>
    </div>
    <div class="col-md-8">
                <?php echo $this->Request->tableRequests($requests);?>
    </div>
</div>


<?php echo $this->Html->script('Requests/add.js');?>
