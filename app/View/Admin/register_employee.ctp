<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
        <div class="alert alert-info" role="alert"><p><strong>Als je meerdere gebruikers wilt aanmaken</strong>, kun je dat beter met de importeertoepassing doen. Hiermee kun je een Excel-bestand(csv,;) uploaden en door het systeem laten interpreteren.</p><p><a href="<?php echo $this->base;?>/Employees/import">Klik hier om meerdere gebruikers toe te voegen</a></p></div>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een werknemer aanmaken</h2>
        <?php echo $this->Form->create('Employee', array('url' => array('controller' => 'Admin', 'action' => 'registerEmployee')));?>
        <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Voornaam'));?>
        <?php echo $this->Form->input('surname', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Familienaam'));?>
        <hr />
        <?php echo $this->Form->input('employee_department_id', array('label' => false, 'class' => 'form-control spaced', 'options' => $this->Employee->selectorAllEmployeeDepartments($departments, 'array')));?>
        <?php echo $this->Form->input('3gram', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Trigram (of email adres als het Trigram niet gelijk is aan gebruikersTrigram'));?>
        <?php echo $this->Form->input('telephone', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Intern telefoonnummer'));?>
        <?php echo $this->Form->input('supervisor_id', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Verantwoordelijke', 'options' => $this->Employee->selectorAllEmployees($employees, 'array', 1)));?>
        <hr />
        <?php echo $this->Form->input('role_id', array('label' => false, 'placeholder' => 'Rol', 'class' => 'form-control spaced', 'options' => array(array("name" => "Standaardgebruiker", "value" => 3), array("name" => "HR", "value" => 2), )));?>
        <?php echo $this->Form->input('daysleft', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Aantal verlofdagen'));?>
        <?php echo $this->Form->input('internal_id', array('type' => 'text', 'label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Personeelsnummer'));?>
        <?php echo $this->Form->input('indexed_on_schaubroeck', array('options' => array(array('value' => 1, 'name' => 'Wordt verstuurd naar Schaubroeck'), array('value' => 0, 'name' => 'Wordt niet verstuurd naar Schaubroeck')), 'label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Versturen naar Schaubroeck?'));?>
        <?php echo $this->Form->submit('Aanmaken', array('class' => 'btn btn-primary fullwidth'));?>
        <?php echo $this->Form->end();?>

    </div>
</div>