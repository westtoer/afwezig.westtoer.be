<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <div class="row">
            <h2 class="first">Zet verantwoordelijkheid over</h2>
            <p>Om iemands goedkeuringsrechten over te zetten, kies je in onderstaand veld deze werknemer en wie hem vervangt.</p>
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="well">
                    <?php echo $this->Form->create('', array('url' => $this->here));?>
                    <h5>Verantwoordelijke</h5>
                    <select class="form-control" id="Supervisor" name="data[Supervisor]">
                        <?php foreach($this->Employee->selectorAllEmployees($employees, 'html' ,'Verantwoordelijke') as $employee){ echo $employee;};?>
                    </select>
                    <h5>Vervanger</h5>
                    <select class="form-control" id="supReplacement" name="data[Replacement]">
                        <?php foreach($this->Employee->selectorAllEmployees($employees, 'html', 'Vervanger') as $employee){ echo $employee;};?>
                    </select>
                    <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth spaced'));?>
                    <?php echo $this->Form->end();?>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>