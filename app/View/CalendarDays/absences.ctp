<div class="row">
    <div class="col-md-3"><?php echo $this->element('user_sidebar');?></div>
    <div class="col-md-9">
        <h2 class="first">Afwezig op <?php echo date('d-m-Y', strtotime($day));?></h2>

        <?php echo $this->General->absenceToList('AM', $absences);?>
        <?php echo $this->General->absenceToList('PM', $absences);?>
        <?php echo $this->General->absenceToList('Day', $absences);?>
    </div>
</div>
