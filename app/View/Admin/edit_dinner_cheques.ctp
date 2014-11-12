<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php echo $this->Form->create();?>
        <?php echo $this->Admin->tableDinnerCheques($employees, 1);?>
        <?php echo $this->Form->submit('Maaltijdcheques opslaan', array('class' => 'btn btn-primary'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>