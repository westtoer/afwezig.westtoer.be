<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="first"><?php echo $query["Employee"]["name"] . ' ' . $query["Employee"]["surname"];?></h3>
                    <ul>
                        <li>Start: <?php echo $query["CalendarItem"]["start_date"] . ' ' . $query["CalendarItem"]["start_time"];?></li>
                        <li>Einde: <?php echo $query["CalendarItem"]["end_date"] . ' ' . $query["CalendarItem"]["end_time"];?></li>
                        <li>Reden: <?php echo $query["CalendarItemType"]["name"];?></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <a href="/CalendarItems/approve/<?php echo $query['CalendarItem']['id'];?>" class="btn btn-success fullwidth spaced">Goedkeuren</a>
                    <a href="/CalendarItems/approve/<?php echo $query['CalendarItem']['id'];?>" class="btn btn-danger fullwidth spaced">Weigeren</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="well flat">
            <h3 class="first">Vorige verlofdagen</h3>
            <ul>
                <?php foreach($previous as $p):?>
                    <li><?php echo $p["CalendarItem"]["start_date"] . ' ' . $p["CalendarItem"]["start_time"] . ' - ' . $p["CalendarItem"]["end_date"] . ' ' . $p["CalendarItem"]["end_time"];?>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>
<hr />
<div class="row">
    <?php foreach($overlap as $o):?>
        <div class="col-md-6">
            <div class="well flat">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="first"><?php echo $o["Employee"]["name"];?></h3>
                        <ul>
                            <li>Start: <?php echo $p["CalendarItem"]["start_date"] . ' ' . $p["CalendarItem"]["start_time"];?></li>
                            <li>Einde: <?php echo $p["CalendarItem"]["end_date"] . ' ' . $p["CalendarItem"]["end_time"];?></li>
                            <li>Reden: <?php echo $p["CalendarItemType"]["name"];?></li>
                        </ul>
                    </div>
                     <div class="col-md-4">
                        <h4><?php echo $this->CalendarItem->isApproved($p["CalendarItem"]["approved"]);?></h4>
                     </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>