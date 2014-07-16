


<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="well">
            <h2>UiTID linken?</h2>
            <p>Ben je zeker dat je het UiTID met het email-adres <?php echo base64_decode($this->request->params['named']['email']);?> wilt koppelen?</p>
            <?php echo $this->Form->create(null, array('url' => array('controller' => 'Users', 'action' => 'associate')));?>
            <?php echo $this->Form->hidden('uitid', array('value' => $this->request->params['named']['uitid']));?>
            <?php echo $this->Form->hidden('employeeId', array('value' => $this->request->params['named']['assoc']));?>
            <?php echo $this->Form->hidden('userEmail', array('value' => $this->request->params['named']['email']));?>
            <?php echo $this->Form->submit('Bevestig', array('class' => 'btn btn-primary fullwidth spaced'));?>
            <?php echo $this->Form->end();?>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>