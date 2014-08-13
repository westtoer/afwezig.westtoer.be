<div class="row">
    <div class="col-md-9">
        <h2 class="first">Maaltijdcheques voor <?php echo $this->CalendarDay->toMonthLocale($this->request->query['month']);?></h2>
        <?php echo $this->Admin->tableDinnerCheques($employees);?>
    </div>
</div>