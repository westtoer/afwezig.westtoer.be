<?php $x = $this->CalendarDay->report($calendarDays, $range);?>

<div class="row">
    <div class="col-md-3">

        <div class="daysleft">
            <h2><?php echo ($x["size"]/2);?></h2>
            <p>Dagen afwezig</p>
        </div>
        <div class="daysleft">
            <h2><?php echo ($offDays/2);?></h2>
            <p>Verlofdagen gebruikt</p>
        </div>s
        <a href="<?php $this->base?>/Admin/generateReportEmployee/<?php echo $id;?>.pdf?<?php if(isset($this->request->query["month"])){ echo '&month=' . $this->request->query["month"] ;}?><?php if(isset($this->request->query["type"])){ echo '&type=' . $this->request->query["type"];}?>">Download als Pdf</a>
    </div>
    <div class="col-md-9">
        <h2 class="first"><?php echo $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"];?>  <?php if(isset($this->request->query["month"])){echo 'in ' . $this->CalendarDay->toMonthLocale($this->request->query["month"]);};?></h2>
        <?php echo $x["html"];?>
    </div>
</div>

