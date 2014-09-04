<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
        <div class="alert alert-info" role="alert"><p><strong>Als je meerdere gebruikers wilt aanmaken</strong>, kun je dat beter met de importeertoepassing doen. Hiermee kun je een Excel-bestand(csv,;) uploaden en door het systeem laten interpreteren.</p><p><a href="<?php echo $this->base;?>/Employees/import">Klik hier om meerdere gebruikers toe te voegen</a></p></div>
        <div class="alert alert-warning" role="alert"><p><strong>Een trigram moet altijd uniek zijn!</strong> Als je een jobstudent wilt toevoegen, gebruik dan zijn westtoer e-mail adres in plaats van het hergebruikte trigram.</p></div>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een werknemer aanmaken</h2>
        <?php echo $this->Form->create('Employee', array('url' => array('controller' => 'Admin', 'action' => 'registerEmployee')));?>
        <?php echo $this->Form->input('name', array('label' => 'Voornaam', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('surname', array('label' => 'Familienaam', 'class' => 'form-control spaced'));?>
        <hr />
        <?php echo $this->Form->input('employee_department_id', array('label' => 'Dienst', 'class' => 'form-control spaced', 'options' => $this->Employee->selectorAllEmployeeDepartments($departments, 'array')));?>
        <?php echo $this->Form->input('3gram', array('label' => 'Trigram', 'class' => 'form-control spaced', 'placeholder' => 'Trigram (of email adres als het Trigram niet gelijk is aan gebruikersTrigram'));?>
        <?php echo $this->Form->input('telephone', array('label' => 'Telefoon', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('supervisor_id', array('label' => 'Verantwoordelijke', 'class' => 'form-control spaced', 'options' => $this->Employee->selectorAllEmployees($employees, 'array', 1)));?>
        <hr />
        <?php echo $this->Form->input('role_id', array('label' => 'Rol', 'class' => 'form-control spaced', 'options' => array(array("name" => "Standaardgebruiker", "value" => 3), array("name" => "HR", "value" => 2), array("name" => "Administrator", "value" => 1))));?>
        <?php echo $this->Form->input('daysleft', array('label' => 'Aantal halve verlofdagen', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('internal_id', array('type' => 'text', 'label' => 'Personeelsnummer', 'class' => 'form-control spaced'));?>
        <hr />
        <?php echo $this->Form->input('dinner_cheques', array('options' => array(array('value' => 1, 'name' => 'Recht op maaltijdcheques'), array('value' => 0, 'name' => 'Geen recht op maaltijdcheques')), 'label' => 'Maaltijdcheques', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('indexed_on_schaubroeck', array('options' => array(array('value' => 1, 'name' => 'Wordt verstuurd naar Schaubroeck'), array('value' => 0, 'name' => 'Wordt niet verstuurd naar Schaubroeck')), 'label' => 'Naar Schaubroeck?', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('status', array('label' => 'Status', 'class' => 'form-control', 'options' => array(array('value' => 1, 'name' => 'Actief'), array('value' => 0, 'name' => 'Niet actief'))));?>
        <?php echo $this->Form->submit('Aanmaken', array('class' => 'btn btn-primary fullwidth spaced'));?>
        <?php echo $this->Form->end();?>

    </div>
</div>
