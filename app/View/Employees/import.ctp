<h3>Werknemers importeren</h3>
<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <h4 class="first">Bestand kiezen</h4>
            <?php
            echo $this->Form->create('Employee', array('action' => 'import', 'type' => 'file') );
            echo $this->Form->input('CsvFile', array('label'=>'','type'=>'file', 'class' => 'form-control', 'style' => 'height: 40px;') );
            echo $this->Form->submit('Importeren', array('class' => 'btn btn-primary fullwidth spaced'));
            echo $this->Form->end();
            ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-info" role="alert">
            <strong>Import sjabloon  - CSV:</strong>
            <p>Om werknemers te importeren, moet je een sjabloon invullen. <a href="<?php echo $this->base;?>/files/employee.csv">Klik hier om dat sjabloon te downloaden.</a></p>
        </div>
    </div>
</div>
