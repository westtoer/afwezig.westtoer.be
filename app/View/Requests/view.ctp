<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="first"><?php echo $query["Employee"]["name"] . ' ' . $query["Employee"]["surname"];?></h3>
                    <ul>
                        <li>Start: <?php echo $query["Request"]["start_date"] . ' ' . $query["Request"]["start_time"];?></li>
                        <li>Einde: <?php echo $query["Request"]["end_date"] . ' ' . $query["Request"]["end_time"];?></li>
                        <li>Reden: <?php echo $query["CalendarItemType"]["name"];?></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo $this->base;?>/Requests/approve/<?php echo $query['Request']['id'];?>" class="btn btn-success fullwidth spaced">Goedkeuren</a>
                    <a href="<?php echo $this->base;?>/Requests/approve/<?php echo $query['Request']['id'];?>" class="btn btn-danger fullwidth spaced">Weigeren</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="well flat">
            <h3 class="first">Vorige verlofdagen</h3>
            <ul>
                <?php foreach($previous as $p):?>
                <li><?php echo $p["Request"]["start_date"] . ' ' . $p["Request"]["start_time"] . ' - ' . $p["Request"]["end_date"] . ' ' . $p["Request"]["end_time"];?>
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
                        <h3 class="first"><?php echo $o["Employee"]["name"] . ' ' . $o["Employee"]["surname"];?></h3>
                        <ul>
                            <li>Start: <?php echo $o["Request"]["start_date"] . ' ' . $o["Request"]["start_time"];?></li>
                            <li>Einde: <?php echo $o["Request"]["end_date"] . ' ' . $o["Request"]["end_time"];?></li>
                            <li>Reden: <?php echo $o["CalendarItemType"]["name"];?></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h4><?php echo $this->Request->isApproved($o["AuthItem"]["authorized"], $o["AuthItem"]["authorization_date"]);?></h4>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>