<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('user_sidebar');?>
        <div class="well flat">
            <h3 class="first">Feestdagen</h3>
            <ul class="nulled striped">
                <?php
                foreach($holidays as $holiday){
                    echo '<li><strong>' . $holiday["Request"]["name"] . '</strong><br/> ' . $holiday["Request"]["start_date"] . ' ' . $holiday["Request"]["start_time"] . '  -  ' . $holiday["Request"]["end_date"] . ' ' . $holiday["Request"]["end_time"] . '</li>';
                     }
                ?>
            </ul>

        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">
                <h2 class="first">Vandaag</h2>
                <ul class="list-group">
                <?php
                echo $this->General->absenceToList('AM', $absences);
                echo $this->General->absenceToList('PM', $absences);
                echo $this->General->absenceToList('Day', $absences);



                ;?>
                </ul>
            </div>
            <div class="col-md-6">
                <h2 class="first">Volgende afwezigheid</h2>
                <div class="well flat">
                    <p>Uw volgende afwezigheid die is goedgekeurd valt op <strong><?php echo date('d-m-Y', strtotime($nextRequest["Request"]["start_date"])) . ' ' . $nextRequest["Request"]["start_time"];?></strong> en duurt tot <strong><?php echo date('d-m-Y', strtotime($nextRequest["Request"]["end_date"])) . ' ' . $nextRequest["Request"]["end_time"];?></strong></p>
                </div>
            </div>
        </div>
        <hr />
        <?php
        echo $this->CalendarDay->tableCalendarDays($absencesThisWeek);
        ?>
        <div class="row">
            <div class="col-md-6"><a href="<?php echo $this->base;?>/?start=<?php echo $navigate["previous"];?>">Vorige week</a></div>
            <div class="col-md-6 right"><a href="<?php echo $this->base;?>/?start=<?php echo $navigate["next"];?>">Volgende week</a></div>
        </div>
    </div>
</div>




