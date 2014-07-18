<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('user_sidebar');?>
    </div>
    <div class="col-md-9">
        <ul class="nav nav-pills">
            <li><a href="#Users">Aanmeldgegevens</a></li>
            <li><a href="#Profile">Profiel</a></li>
            <li><a href="#Requests">Aanvragen</a></li>
        </ul>
        <hr />
        <h2 class="first" id="Users">Aanmeldgegevens</h2>
        <p>Op verlof laat toe om met meerdere UiTID's aan te melden. Om een nieuwe UiTID te koppelen aan je gebruiker, meld je gewoon aan met een nog
        voor het systeem onbekende UiTID, en begint het registratieproces opnieuw.</p>
        <br />
        <?php echo $this->User->tableLinkedUsers($linkedUsers);?>
        <hr />
        <h2 id="Profile">Profiel</h2>
        <p>Je kunt op je gebruikersprofiel een notitie achterlaten. Deze kun je hieronder aanpassen.</p>
        <?php echo $this->Form->create('Employee', array(
            'type' => 'post',
            'url' => array('controller' => 'employees', 'action' => 'edit')
        ));?>
        <!-- Don't try to be smart, you can't edit this value and expect to update someone elses note -->
        <?php echo $this->Form->hidden('id', array('value' => $this->Session->read('Auth.Employee.id')));?>
        <div class="row">
            <div class="col-md-7 formspaced-left">
                <?php echo $this->Form->textarea('note', array('value' => $employee["Employee"]["note"], 'class' => 'form-control spaced', 'style' => 'height: 72px;'));?>
            </div>
            <div class="col-md-5 formspaced-right">
                <?php echo $this->Form->input('telephone', array('label' => false,'value' => $employee["Employee"]["telephone"], 'class' => 'form-control spaced', 'placeholder' => 'Uw telefoonnummer'));?>
                <?php echo $this->Form->input('gsm', array('label' => false, 'value' => $employee["Employee"]["gsm"], 'class' => 'form-control spaced', 'placeholder' => 'Uw gsm nummer'));?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 right"><?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary right'));?></div>
        </div>

        <?php echo $this->Form->end();?>
        <hr />
        <h2 id="Requests">Aanvragen</h2>
        <br />
        <?php echo $this->Request->tableRequests($requestsVisible);?>



    </div>
</div>
