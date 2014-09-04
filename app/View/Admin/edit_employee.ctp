<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
        <?php
            $options = array();
            if($employee["Employee"]["indexed_on_schaubroeck"] == 0){
                $options["indexed_on_schaubroeck"] = array(array('value' => 0, 'name' => 'Wordt niet verstuurd naar Schaubroeck'), array('value' => 1, 'name' => 'Wordt verstuurd naar Schaubroeck'));
            } else {
                $options["indexed_on_schaubroeck"] = array(array('value' => 1, 'name' => 'Wordt verstuurd naar Schaubroeck'), array('value' => 0, 'name' => 'Wordt niet verstuurd naar Schaubroeck'));
            }

            if($employee["Employee"]["status"] == 0){
                $options["status"] = array(array('value' => 0, 'name' => 'Niet actief'), array('value' => 1, 'name' => 'Actief'));
            } else {
                $options["status"] = array(array('value' => 1, 'name' => 'Actief'), array('value' => 0, 'name' => 'Niet actief'));
            }

        if($employee["Employee"]["dinner_cheques"] == 0){
            $options["dinner_cheques"] = array(array('value' => 0, 'name' => 'Geen recht op maaltijdcheques'), array('value' => 1, 'name' => 'Recht op maaltijdcheques'));
        } else {
            $options["dinner_cheques"] = array(array('value' => 1, 'name' => 'Recht op maaltijdcheques'), array('value' => 0, 'name' => 'Geen recht op maaltijdcheques'));
        }
        ?>


    </div>
    <div class="col-md-9">
        <div class="row"><div class="col-md-8"><h2 class="first">Een werknemer aanpassen</h2></div><div class="col-md-4 right"><a href="<?php echo $this->here;?>">Wijzigingen ongedaan maken</a></div></div>
        <?php echo $this->Form->create('Employee', array('url' => array('controller' => 'Admin', 'action' => 'editEmployee')));?>
        <?php echo $this->Form->hidden('id', array('value' => $employee["Employee"]["id"]));?>
        <?php echo $this->Form->input('name', array('label' => 'Voornaam', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["name"]));?>
        <?php echo $this->Form->input('surname', array('label' => 'Familienaam', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["surname"]));?>
        <hr />
        <?php echo $this->Form->input('employee_department_id', array('label' => 'Dienst', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["employee_department_id"], 'options' => $this->Employee->selectorAllEmployeeDepartments($departments, 'array')));?>
        <?php echo $this->Form->input('3gram', array('label' => 'Trigram', 'class' => 'form-control spaced', 'placeholder' => 'Trigram (of email adres als het Trigram niet gelijk is aan gebruikerstrigram)', 'value' => $employee["Employee"]["3gram"]));?>
        <?php echo $this->Form->input('telephone', array('label' => 'Intern telefoonnummer', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["telephone"]));?>
        <?php echo $this->Form->input('gsm', array('label' => 'GSM-nummer', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["gsm"]));?>
        <?php echo $this->Form->input('supervisor_id', array('label' => 'Verantwoordelijke', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["supervisor_id"], 'options' => $this->Employee->selectorAllEmployees($employees, 'array', 1)));?>
        <hr />
        <?php echo $this->Form->input('role_id', array('label' => 'Rol', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["role_id"], 'options' => array(array("name" => "Standaardgebruiker", "value" => 3), array("name" => "HR", "value" => 2), array("name" => "Administrator", "value" => 1) )));?>
        <div class="row"><div class="col-md-8"><h4>Aantal halve dagen verbruikt: <?php echo $prevCost;?>(<?php echo $employee["Employee"]["daysleft"];?> - <?php echo $prevCost;?> = <?php echo $employee["Employee"]["daysleft"] - $prevCost;?> <small>resterend</small>)</h4></div><div class="col-md-4"><?php echo $this->Form->input('daysleft', array('label' => 'Aantal halve verlofdagen', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["daysleft"]));?></div></div>
        <?php echo $this->Form->input('internal_id', array('type' => 'text', 'label' => 'Personeelsnummer', 'class' => 'form-control spaced', 'value' => $employee["Employee"]["internal_id"]));?>
        <hr />
        <?php echo $this->Form->input('dinner_cheques', array('options' => $options["dinner_cheques"], 'class' => 'form-control spaced', 'label' => 'Maaltijdcheques?'));?>
        <?php echo $this->Form->input('indexed_on_schaubroeck', array('options' => $options["indexed_on_schaubroeck"], 'label' => 'Naar Schaubroeck?', 'class' => 'form-control spaced'));?>
        <?php echo $this->Form->input('status', array('label' => 'Status', 'class' => 'form-control', 'options' => $options["status"]));?>
        <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth spaced'));?>
        <?php echo $this->Form->end();?>

    </div>
</div>