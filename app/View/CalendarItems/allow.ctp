<?php if (!empty($toBeAllowed)):?>
    <div class="row">
        <?php foreach($toBeAllowed as $CalendarItem):?>
             <div class="col-md-6">
                  <div class="well flat">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="null"><?php echo $CalendarItem["Employee"]["name"] . ' ' . $CalendarItem["Employee"]["surname"] ?></h3>
                                <p>Heeft <?php echo $CalendarItem["CalendarItemType"]["name"];?> aangevraagd vanaf <?php echo date('d-m-Y', strtotime($CalendarItem["CalendarItem"]["start_date"])) . ' ' . $CalendarItem["CalendarItem"]["start_time"];?> tot en met <?php echo date('d-m-Y', strtotime($CalendarItem["CalendarItem"]["end_date"])) . ' ' . $CalendarItem["CalendarItem"]["end_time"] ;?>.</p>
                                <p></p>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo $this->base . '/CalendarItems/overlap/' . $CalendarItem["CalendarItem"]["id"];?>" class="btn btn-primary fullwidth spaced">Bekijk overlap</a>
                                <a href="<?php echo $this->base . '/CalendarItems/approved/' . $CalendarItem["CalendarItem"]["id"];?>" class="btn btn-success fullwidth spaced">Accepteer</a>
                                <a href="<?php echo $this->base . '/CalendarItems/delete/' . $CalendarItem["CalendarItem"]["id"];?>" class="btn btn-danger fullwidth spaced">Weiger</a>
                            </div>
                        </div>
                  </div>
              </div>
        <?php endforeach;?>
    </div>
<?php endif;?>