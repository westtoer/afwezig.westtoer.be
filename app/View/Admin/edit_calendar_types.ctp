<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <div class="well flat">
            <div class="row">
                <div class="col-md-8">Nieuwe kalendertypes kunnen on the fly worden toegevoegd door de knop Nieuw te gebruiken.</div>
                <div class="col-md-4"><a onClick="newType()" class="btn btn-success fullwidth">Nieuwe toevoegen</a></div>
            </div>
        </div>
        <?php echo $this->Admin->tableCalendarItemTypes($calendarTypes)?>
        <?php echo $this->Html->script('Admin/addCalendarTypes');?>
    </div>
</div>