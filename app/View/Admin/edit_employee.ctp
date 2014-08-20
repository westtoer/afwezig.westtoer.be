<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een werknemer aanpassen</h2>
        <?php echo $this->Form->create('Employee', array('url' => array('controller' => 'Admin', 'action' => 'editEmployee')));?>
        <?php echo $this->Form->hidden('id', array('value' => $employee["Employee"]["id"]));?>
        <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Voornaam', 'value' => $employee["Employee"]["name"]));?>
        <?php echo $this->Form->input('surname', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Familienaam', 'value' => $employee["Employee"]["surname"]));?>
        <hr />
        <?php echo $this->Form->input('employee_department_id', array('label' => false, 'class' => 'form-control spaced', 'value' => $employee["Employee"]["employee_department_id"], 'options' => $this->Employee->selectorAllEmployeeDepartments($departments, 'array')));?>
        <?php echo $this->Form->input('3gram', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Trigram (of email adres als het Trigram niet gelijk is aan gebruikerstrigram)', 'value' => $employee["Employee"]["3gram"]));?>
        <?php echo $this->Form->input('telephone', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Intern telefoonnummer', 'value' => $employee["Employee"]["telephone"]));?>
        <?php echo $this->Form->input('gsm', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'GSM-nummer', 'value' => $employee["Employee"]["gsm"]));?>
        <?php echo $this->Form->input('supervisor_id', array('label' => false, 'class' => 'form-control spaced', 'value' => $employee["Employee"]["supervisor_id"], 'options' => $this->Employee->selectorAllEmployees($employees, 'array', 1)));?>
        <hr />
        <?php echo $this->Form->input('role_id', array('label' => false, 'placeholder' => 'Rol', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["role_id"], 'options' => array(array("name" => "Standaardgebruiker", "value" => 3), array("name" => "HR", "value" => 2), array("name" => "Administrator", "value" => 1) )));?>
        <?php echo $this->Form->input('daysleft', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Aantal halve verlofdagen', 'value' => $employee["Employee"]["daysleft"]));?>
        <?php echo $this->Form->input('internal_id', array('type' => 'text', 'label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Personeelsnummer', 'value' => $employee["Employee"]["internal_id"]));?>
        <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth'));?>
        <?php echo $this->Form->end();?>

    </div>
</div>