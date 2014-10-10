<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
        <a href="<?php echo $this->base;?>/Admin/dinnerCheques.pdf?month=<?php echo $month;?>">Downloaden als PDF</a>
    </div>
    <div class="col-md-9">

            <h2 class="first">Maaltijdcheques voor <?php echo $this->CalendarDay->toMonthLocale($month);?></h2>
            <?php if($showPersist == true){ ?>
            <div class="well flat">
                <div class="row">
                    <div class="col-md-8">
                        <p>Het is belangrijk om de maaltijdcheques te persisteren in de database, nadat je ze doorgegeven hebt aan Edenred.</p>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo $this->here;?>?month=<?php echo $month;?>&persist=true" class="btn btn-success fullwidth">Persisteer</a>
                    </div>
                </div>
            </div>

            <?php }?>
            <?php echo $this->Admin->tableDinnerCheques($employees);?>
    </div>
</div>