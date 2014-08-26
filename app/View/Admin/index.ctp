<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6"><h2 class="first">Werknemers & Gebruikers</h2></div>
            <div class="col-md-6">
                <ul class="nulled">
                    <li><a href="<?php echo $this->base;?>/Admin/registerEmployee">Nieuwe werknemer toevoegen</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/viewEmployees">Alle werknemers/rapporten genereren</a></li>
                    <li><a href="<?php echo $this->base;?>/Employees/import">Werknemers importeren via CSV</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/viewRegistrations">Nieuwe registraties goedkeuren</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/viewUsers">Alle gebruikers</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/editCalendarDays">Ingrijpen op kalender</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/departments">Afdelingen</a></li>
                </ul>
            </div>
        </div> <hr />

        <div class="row">
            <div class="col-md-6"><h2 class="first">Afwezigheidsaanvragen</h2></div>
            <div class="col-md-6">
                <ul class="nulled">
                    <li><a href="<?php echo $this->base;?>/Admin/viewPendingCalendarItems">Goedkeuren</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/export">Overzicht/Export</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/dinnerCheques">Maaltijdcheques</a></li>
                </ul>
            </div>
        </div> <hr />

        <div class="row">
            <div class="col-md-6"><h2 class="first">Algemene instellingen</h2></div>
            <div class="col-md-6">
                <ul class="nulled">
                    <li><a href="<?php echo $this->base;?>/Admin/GeneralCalendarItems">Algemene feestdagen beheren</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/endOfYear">Einde van het jaar boeken</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/lockApp">Applicatie openen/sluiten</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/editCalendarTypes">Dagcodes wijzigen</a></li>
                </ul>
            </div>
        </div> <hr />

        <div class="row">
            <div class="col-md-6"><h2 class="first">Stramienen</h2></div>
            <div class="col-md-6">
                <ul class="nulled">
                    <li><a href="<?php echo $this->base;?>/Admin/addStream">Toevoegen</a></li>
                    <li><a href="<?php echo $this->base;?>/Admin/viewStreams">Alle Stramienen</a></li>
                </ul>
            </div>
        </div> <hr />

        </div>
    </div>
</div>
