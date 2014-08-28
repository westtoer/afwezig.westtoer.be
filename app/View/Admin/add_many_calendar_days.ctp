<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een selectie kalenderdagen wijzigen</h2>
        <p>Om een groot aantal kalenderdagen te wijzigen, gebruik je onderstaand formulier. Alle dagen die tussen de start- en einddatum vallen zullen gewijzigd worden naar
        het gekozen type.</p>
        <p>Indien je wenst een aanvraag in te dienen als een ander persoon, vink je naast de werknemersselectie het veld "In flow?" aan. De kalender wordt dan pas gewijzigd
        wanneer de verantwoordelijke goedkeuring geeft.</p>
        <?php echo $this->Form->create('Request', array('url' => $this->here));?>
        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <h4 class="first">Toepassen op</h4>
                    <?php echo $this->Form->input('employee_id', array('onchange' => 'isEmployeeSet()', 'label' => false, 'class' => 'form-control spaced', 'options' => array($this->Employee->selectorAllEmployees($employees, 'array', 3))));?>
                    <?php echo $this->Form->hidden('origin', array('value' => 'AdminPanel'));?>
                    <?php echo $this->Form->hidden('destination', array('value' => 'true'));?>
                    <select id="inflow" OnChange="setInFlow()" class="form-control">
                        <option value="0">Niet in flow</option>
                        <option value="1">In flow</option>
                    </select>
                </div>
                <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth', 'id' => 'SubmitButton'));?>
            </div>
            <div class="col-md-6">
                <div class="well">
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
                </div>
            </div>
        </div>

        <?php echo $this->Form->end();?>
    </div>
</div>

<?php echo $this->Html->script('Requests/add.js');?>
<?php echo $this->Html->script('Admin/flowDirector.js');?>