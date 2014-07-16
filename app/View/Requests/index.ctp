<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('user_sidebar');?>

    </div>
    <div class="col-md-9">
        <?php if (!empty($requests)):?>
            <div class="row">
                <?php foreach($requests as $request):?>
                    <div class="col-md-6">
                        <div class="well flat">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="null"><?php echo $request["Employee"]["name"] . ' ' . $request["Employee"]["surname"] ?></h3>
                                    <p>Heeft <?php echo $request["CalendarItemType"]["name"];?> aangevraagd vanaf <?php echo date('d-m-Y', strtotime($request["Request"]["start_date"])) . ' ' . $request["Request"]["start_time"];?> tot en met <?php echo date('d-m-Y', strtotime($request["Request"]["end_date"])) . ' ' . $request["Request"]["end_time"] ;?>.</p>
                                    <p></p>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo $this->base . '/Requests/view/' . $request["Request"]["id"];?>" class="btn btn-primary fullwidth spaced">Bekijk overlap</a>
                                    <a href="<?php echo $this->base . '/Requests/allow/' . $request["Request"]["id"];?>" class="btn btn-success fullwidth spaced">Accepteer</a>
                                    <a href="<?php echo $this->base . '/Requests/deny/' . $request["Request"]["id"];?>" class="btn btn-danger fullwidth spaced">Weiger</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        <?php endif;?>
    </div>
</div>
