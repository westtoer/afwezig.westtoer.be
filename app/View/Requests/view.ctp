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
                    <a href="<?php echo $this->base;?>/Requests/allow/<?php echo $query['Request']['id'];?>" class="btn btn-success fullwidth spaced">Accepteer</a>
                    <a href="<?php echo $this->base;?>/Requests/deny/<?php echo $query['Request']['id'];?>" class="btn btn-danger fullwidth spaced">Weiger</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="well flat">
            <h3 class="first">Vorige verlofaanvragen</h3>
            <ul>
                <?php foreach($previous as $p):?>
                <li><?php echo $p["CalendarItemType"]["name"] . ': ' . $p["Request"]["start_date"] . ' ' . $p["Request"]["start_time"] . ' - ' . $p["Request"]["end_date"] . ' ' . $p["Request"]["end_time"];?>
                    <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>
<hr />

<div class="scroll-container">
    <?php echo $this->Request->tableOverlap($overlap, $queryRange);?>
</div>
<p>Hebben in deze periode ook een verlofaanvraag gedaan die nog niet is goedgekeurd:
    <?php
        foreach($overlapRequests as $overlapRequest){
            echo '<a href="/Requests/view/' . $overlapRequest["Request"]["id"] . '">' .$overlapRequest["Employee"]["name"] . ' ' . $overlapRequest["Employee"]["surname"] . '</a>';
        }
    ;?>
</p>