<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php if(isset($this->request->query["employee"])){;?>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#allowed" role="tab" data-toggle="tab">Home</a></li>
                <li><a href="#denied" role="tab" data-toggle="tab">Profile</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="allowed">
                    <?php echo $this->Admin->tableAuthorisations($aiAllowed);?>
                </div>
                <div class="tab-pane" id="denied">
                    <?php echo $this->Admin->tableAuthorisations($aiDenied);?>
                </div>
            </div>
        <?php } else {?>

        <?php } ?>
    </div>
</div>