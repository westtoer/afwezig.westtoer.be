<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php echo $this->Form->create('Request', array('url' => $this->here));?>
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
    </div>
</div>

<?php echo $this->Html->script('Requests/add.js');?>