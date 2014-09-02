<?php $x = $this->CalendarDay->report($calendarDays, $range);?>
<h2 class="first"><?php echo $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"];?>  <?php if(isset($this->request->query["month"])){echo 'in ' . $this->CalendarDay->toMonthLocale($this->request->query["month"]);};?></h2>
<hr />
    <table>
        <tr><td width="33%">
                <h2><?php echo ($x["size"]/2);?></h2>
                <p>Dagen afwezig</p>
            </td>
            <td width="33%">
                <h2><?php echo ($offDays/2);?></h2>
                <p>Verlofdagen gebruikt</p>
            </td>
        </tr>
    </table>
<hr />
    <div class="pdf-page">
        <?php echo $x["html"];?>
        <a href="<?php echo Configure::read('Administrator.base_fallback_url');?>/Admin/generateReportEmployee/<?php echo $id;?><?php if(isset($this->request->query["month"])){ echo '?month=' . $this->request->query["month"];}?>">Bekijk op Afwezig</a>  |  Pdf gemaakt op <?php echo date('Y-m-d');?>
    </div>




