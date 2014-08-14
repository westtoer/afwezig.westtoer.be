<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="well">
            <h2 class="first">Administratierechten toekennen</h2>
            <p>Vermits u de eerste gebruiker bent die aanmeldt, zal u administratierechten toegekent worden. Gelieve dit formulier in te vullen, zodat we alles kunnen klaarzetten voor gebruik.</p>


            <?php echo $this->Form->create('Employee', array('url' => $this->here));?>
            <?php echo $this->Form->input('3gram', array('label' => false, 'placeholder' => 'Uw 3gram', 'class' => 'form-control'));?>
            <?php echo $this->Form->input('name', array('label' => false, 'placeholder' => 'Uw voornaam', 'class' => 'form-control'));?>
            <?php echo $this->Form->input('surname', array('label' => false, 'placeholder' => 'Uw familienaam', 'class' => 'form-control'));?>
            <?php echo $this->Form->input('internal_id', array('label' => false, 'placeholder' => 'Uw personeelsnummer', 'class' => 'form-control'));?>
            <?php echo $this->Form->submit('Registreren', array('class' => 'btn btn-primary fullwidth'));?>
            <?php echo $this->Form->end();?>

        </div>
    </div>
    <div class="col-md-3"></div>
</div>