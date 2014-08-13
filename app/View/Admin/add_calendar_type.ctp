<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een Dagcode toevoegen</h2>
        <?php echo $this->Form->create('CalendarItemType', array('url' => $this->here));?>
        <table class="table">
            <tr><td width="30%">Naam</td><td><input type="text" name="data[CalendarItemType][name]" class="form-control"></td></tr>
            <tr><td width="30%">Code</td><td><input type="text" name="data[CalendarItemType][code]" class="form-control"></td></tr>
            <tr><td width="30%">Zichtbaar</td><td width="20px"><select name="data[CalendarItemType][user_allowed]" class="form-control"><option value="0">Nee</option><option value="1">Ja</option></select></td></tr>
            <tr><td width="30%">Maaltijdcheque</td><td><select name="data[CalendarItemType][dinner_cheque]" class="form-control"><option value="0">Nee</option><option value="1">Ja</option></select></td></tr>
            <tr><td width="30%">Code Schaubroek</td><td><input type="text" name="data[CalendarItemType][code_schaubroek]" class="form-control"></td></tr>
            <tr><td width="30%">Aard Schaubroek</td><td><input type="text" name="data[CalendarItemType][aard_schaubroek]" class="form-control"></td></tr>
            <tr><td width="30%">Extensie Schaubroek</td><td><input type="text" name="data[CalendarItemType][ext_schaubroek]" class="form-control"></td></tr>
        </table>
        <?php echo $this->Form->submit("Dagcode opslaan", array('class' => 'btn btn-primary fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>