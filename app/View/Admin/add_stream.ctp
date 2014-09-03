<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een nieuw stramien toevoegen</h2>
        <?php echo $this->Form->create('Stream', array('url' => '/Admin/addStream'));?>
        <!-- Controls -->
        <div class="well flat">
            <div class="row">
                <div class="col-md-6 formspaced-left">
                    <select class="form-control" id="employee_id" name="data[Stream][employee_id]">
                    <?php foreach($this->Employee->selectorAllEmployees($employees, 'html', 1) as $option){
                        echo $option;
                    };?>
                    </select>
                </div>
                <div class="col-md-6 formspaced-right">
                    <a OnClick="toggleAssymetric()" id="assymetry" class="btn btn-primary fullwidth">Tweewekelijks</a>
                </div>

            </div>
        </div>

        <!-- Form -->

        <?php echo $this->Stream->addStream($calendaritemtypes);?>
        <?php echo $this->Form->Submit('Opslaan', array('class' => 'btn btn-success fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>

<?php echo $this->Html->script('Admin/newStream');?>