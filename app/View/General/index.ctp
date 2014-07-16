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
                <h2 class="first">Volgend verlof</h2>
            </div>
        </div>
        <hr />
        <?php
        echo $this->CalendarDay->tableCalendarDays($absencesThisWeek);
        ?>
    </div>
</div>

<?php var_dump($absencesThisWeek);?>