<div class="panel panel-default">
    <div class="panel-heading">Verlof Aanvragen</div>
    <div class="panel-body">
        <?php
        foreach($users as $User){

            $options[] = array('name' => $User["User"]["name"] . ' ' . $User["User"]["surname"], 'value' => $User["User"]["id"]);
        }
        ?>
        <?php echo $this->Form->create(null, array(
            'url' => array('controller' => 'verlofs', 'action' => 'add')
        ))?>
        <div class="input-group fullwidth spaced">
            <span class="input-group-addon">Start</span>
            <?php echo $this->Form->input('start', array('class' => 'form-control', 'type' => 'datetime-local', 'label' => false));?>
        </div>
        <div class="input-group fullwidth spaced">
            <span class="input-group-addon">Einde</span>
            <?php echo $this->Form->input('end', array('class' => 'form-control', 'type' => 'datetime-local', 'label' => false));?>
        </div>
        <?php echo $this->Form->input('replacement_id', array('class' => 'form-control spaced', 'label' => false,
            'options' => $options))
        ;?>
        <?php echo $this->Form->input('note', array('class' => 'form-control spaced', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Notitie'));?>
        <?php echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User.id')));?>
        <?php echo $this->Form->hidden('allowed', array('value' => 0));?>
        <?php echo $this->Form->submit('Vraag mijn verlof aan', array('class' => 'btn btn-primary fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>
