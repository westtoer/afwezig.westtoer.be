<?php if(!isset($this->request->query['action'])){?>

    <div class="row">
        <div class="col-md-3">
            <?php echo $this->element('admin/base_admin_menu');?>
            <div class="well">
                <h4 class="first">Nieuw</h4>
                <?php echo $this->Form->create('EmployeeDepartment', array('url' => $this->here));?>
                <input type="hidden" id="id-hidden" value="0" name="EmployeeDepartment[id]">
                <input type="text" class="form-control spaced" id="name-input" name="EmployeeDepartment[name]">
                <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth'));?>
                <?php echo $this->Form->end();?>
            </div>
        </div>
        <div class="col-md-9">

            <?php echo $this->Admin->tableDepartments($departments)?>
        </div>
    </div>


<?php } elseif($this->request->query['action'] == 'edit'){?>
    <div class="row">
        <div class="col-md-3"><?php echo $this->element('admin/base_admin_menu');?></div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="well">
                        <h3>Wijzig een dienst</h3>
                        <?php echo $this->Form->create('EmployeeDepartment', array('url' => $this->here));?>
                        <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control'));?>
                        <?php echo $this->Form->hidden('id', array('value' => $this->request->query['id']));?>
                        <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth spaced'));?>
                        <?php echo $this->Form->end();?>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>


<?php }?>