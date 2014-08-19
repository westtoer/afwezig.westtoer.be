<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Stramienen</h2>
        <div class="well flat">
            <div class="row">
                <div class="col-md-8">Stramienen laten je toe om herhaling in werkschema's te verwerken. Eens je eens Stramien hebt gemaakt, moet je deze nog manueel toepassen.</div>
                <div class="col-md-4"><a href="/Admin/addStream" class="btn btn-success fullwidth">Nieuw</a></div>
            </div>
        </div>
        <?php echo $this->Stream->tableStreams($employees);?>
    </div>
</div>