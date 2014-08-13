<?php $x = $this->CalendarDay->reportAll($calendarDays, $range);?>

<div class="row">
    <div class="col-md-3">
        <a href="<?php $this->base?>/Admin/generateReportEmployee/<?php echo $id;?>.pdf?<?php if(isset($this->request->query["month"])){ echo '&month=' . $this->request->query["month"] ;}?><?php if(isset($this->request->query["type"])){ echo '&type=' . $this->request->query["type"];}?>">Download als Pdf</a>
    </div>
    <div class="col-md-9">
        <?php echo $x["html"];?>
    </div>
</div>

