<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
        <a href="">Downloaden als PDF</a>
    </div>
    <div class="col-md-9">
        <?php if(isset($this->request->query['month'])){?>
            <h2 class="first">Maaltijdcheques voor <?php echo $this->CalendarDay->toMonthLocale($this->request->query['month']);?></h2>
            <?php echo $this->Admin->tableDinnerCheques($employees);?>
        <?php } else {?>
            <h2>Genereer de maaltijdchequeberekening</h2>
            <p>Kies een periode voor waarop de maaltijdcheques op kunnen worden berekent.</p>
            <div class="row">
                <div class="col-md-3 formspaced-left">
                    <select class="form-control" id="month">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maart</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Augustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="col-md-3 formspaced-right">
                    <a onClick="goToDinnerCheque()" class="btn btn-primary fullwidth">Genereer rapport</a>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
            </div>


            <script>
                function goToDinnerCheque(){
                    var month = $('#month').val();
                    window.location.href = '<?php $this->here;?>?month=' + month;
                }
            </script>


        <?php }?>
    </div>
</div>