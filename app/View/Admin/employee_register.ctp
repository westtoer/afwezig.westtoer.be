<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <?php echo $this->Form->create('Employee', array('url' => array('controller' => 'admin', 'action' => 'EmployeeRegister')));?>
            <?php echo $this->Form->input('Name', array('label' => false, 'placeholder' => 'Voornaam', 'class' => 'form-control spaced'));?>
            <?php echo $this->Form->input('Surname', array('label' => false, 'placeholder' => 'Familienaam', 'class' => 'form-control spaced'));?>
            </hr>
            <?php echo $this->Form->input('Rol', array('label' => false, 'placeholder' => 'Rol', 'class' => 'form-control spaced', 'options' => array(array("name" => "Standaardgebruiker", "value" => 3), array("name" => "HR", "value" => 2), )));?>

            <?php echo $this->Form->end("Verstuur");?>
        </div>
    </div>
</div>