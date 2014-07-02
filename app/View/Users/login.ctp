<?php echo $this->Session->flash('auth'); ?>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="spacer"></div>
            <div class="logo-wrapper">
                <?php echo $this->Html->image('westtoer-logo.jpg', array('alt' => 'Westtoer'));?>
                <h2> op verlof</h2>
            </div>
            <div class="well">
                <h2>Inloggen<span class="pull-right"><span class="glyphicon glyphicon-info-sign" style="font-size: 14px; font-weight: normal;"></span></span></h2>
                <?php echo $this->Form->create('User'); ?>
                <div class="form-group fullwidth">
                    <?php echo $this->Form->input('username', array('class' => 'form-control', 'label' => false, 'type' => 'text', 'placeholder' => 'Gebruikersnaam'));?>
                </div>
                <div class="form-group fullwidth">
                    <?php echo $this->Form->input('password', array('class' => 'form-control', 'label' => false, 'type' => 'password', 'placeholder' => 'Wachtwoord'));?>
                </div>
                <?php echo $this->Form->submit(__('Login'), array('class' => 'btn btn-primary fullwidth')); ?>
                <?php echo $this->Form->end();?>
            </div>
            <div class="well">
                Westtoer op Verlof is de verloftoepassing van Westtoer. Hiermee kun je bekijken wie er wanneer op verlof is, en wie je moet contacteren, alsook je eigen verlof aanvragen.
                <div class="spacer"></div>
                <div class="row">
                    <div class="col-md-6"><a href="">Keer terug naar het Intranet</a></div>
                    <div class="col-md-6"><a href="">Lees de documentatie over 'Op Verlof' </a></div>
                </div>
            </div>

        </div>
        <div class="col-md-3"></div>
    </div>
