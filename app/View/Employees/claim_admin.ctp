<h2 class="first">Afwezig: Installatie</h2>
<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <h4>Uw installatie is bijna klaar!</h4>
            <p>Wanneer u deze pagina ziet, hebt u de installatie van de afwezigheidsapplicatie goed doorlopen.</p>
            <p>Het enige wat nu nog moet gebeuren, is het aanmaken van een administratie-account.</p>
            <p>We hebben uw UiTID al, het enige wat we nog moeten weten, is wat uw werknemersaccount is.</p>
        </div>
    </div>
    <div class="col-md-6">
        <p>Uw UiTID: <code><?php echo base64_decode($this->request->params["named"]["uitid"]);?></code></p>
        <p>Uw E-mail: <code><?php echo base64_decode($this->request->params["named"]["email"]);?></code></p>
        <hr />
        <?php echo $this->Form->create('Employee', array('url' => 'http://' . $_SERVER["HTTP_HOST"] . $this->base . '/users/claimAdmin'));?>
        <input type="hidden" name="data[User][uitid]" value="<?php echo $this->request->params["named"]["uitid"];?>">
        <input type="hidden" name="data[User][email]" value="<?php echo $this->request->params["named"]["email"];?>">
        <?php echo $this->Form->input('3gram', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Uw 3gram'));?>
        <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Uw (voor)naam'));?>
        <?php echo $this->Form->input('surname', array('label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Uw familienaam'));?>
        <?php echo $this->Form->input('internal_id', array('type' => 'text', 'label' => false, 'class' => 'form-control spaced', 'placeholder' => 'Uw werknemersnummer'));?>
        <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>