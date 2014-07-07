


<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="well">
            <h2>Bevestig uw Email</h2>
            <?php echo $this->Form->create(null, array('url' => array('controller' => 'Users', 'action' => 'associate')));?>
            <?php echo $this->Form->hidden('uitid', array('value' => $this->request->params['named']['uitid']));?>
            <?php echo $this->Form->hidden('employeeId', array('value' => $this->request->params['named']['assoc']));?>
            <?php echo $this->Form->input('userEmail', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Uw UiTID e-mailadres'));?>
            <?php echo $this->Form->submit('Bevestig', array('class' => 'btn btn-primary fullwidth spaced'));?>
            <?php echo $this->Form->end();?>
        </div>
    </div>
    <div class="col-md-3"></div>