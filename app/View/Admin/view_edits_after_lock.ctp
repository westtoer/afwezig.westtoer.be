<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php echo $this->Admin->tableRequests($edits, 0);?>
    </div>
</div>
