<div class="row">
    <div class="col-md-3">
        <div class="alert alert-danger" role="alert"><strong>Stoppen met de wizard</strong><p>Eens je begonnen bent met de wizard, kan je hem alleen stoppen met de bovenstaande link 'Wizard annuleren'.</p></div>
        <div class="alert alert-info" role="alert"><strong>Verlofdagen ingeven</strong><p>Verlofdagen worden als halve dagen ingegeven.</p></div>
    </div>
    <div class="col-md-9">
        <?php if(!isset($step)){?>
            <h1 class="first">Wizard: Einde van het jaar</h1>
            <p>Voor we beginnen, is het belangrijk dat er geen aanpassingen meer gebeuren terwijl we een overboeking aan het doen zijn. Daarom is het belangrijk om
            de database te sluiten. Door de database te sluiten, wordt iedereen automatisch afgemeld en worden alle verlofaanvragen die nog moeten behandeld zijn, geannuleerd.</p><p>Als je klaar bent voor het sluiten van de database, klik je op onderstaande knop, en zal de wizard je automatisch naar de volgende stap
            loodsen.</p>

            <p>Voor je begint, is het echter ten sterkste aangeraden om een backup te nemen van de MySQL-database. Gelieve hiervoor de systeembeheerder te contacteren.</p>
            <center><a href="<?php echo $this->base;?>/admin/endOfYear?step=1" class="btn btn-primary btn-lg">Database afsluiten</a></center>







        <?php } else {?>

            <?php if($step == 1){?>
                <h1 class="first">Halve verlofdagen dit jaar aanpassen</h1>
                <?php echo $this->Employee->tableEndOfYear($employees, 'mutate', 1, 2);?>
            <?php }?>
            <?php if($step == 3){?>
                    <h1 class="first">Halve verlofdagen dit volgend jaar toekennen</h1>
                    <?php echo $this->Employee->tableEndOfYear($employees, 'new', 1, 3);?>
                    <script>
                        function updateFields(){

                            for ( var i = 0; i < amountOfRows; i++) {
                                var valueDirector = $('#director').val();
                                var element = '#Employee' + (i) + 'Daysleft';
                                var valueItem = $(element).data(i + '-daysleft');
                                $(element).val(parseFloat(valueDirector) + parseFloat(valueItem));
                            }
                        }
                    </script>
            <?php }?>
            <?php if($step == 4){?>
                <h1 class="first">Database opruimen?</h1>
                <p>Mag de database opgeruimt worden? Dit betekent dat alle vakantieaanvragen en dagen die ouder zijn dan twee jaar verwijderd worden.</p>
                <div class="row">
                    <div class="col-md-6"><a href="/admin/endOfYear?step=5" class="btn btn-success fullwidth">Ja, ruim de database op</a></div>
                    <div class="col-md-6"><a href="/admin/endOfYear?step=6" class="btn btn-danger fullwidth">Neen, hou alles bij</a></div>
                </div>
            <?php }?>
            <?php if($step == 6){?>
                <h1 class="first">Feestdagen overzetten</h1>
                <p>Wilt u feestdagen van vorig jaar overnemen?</p>
                <?php echo $this->Admin->tableHolidays($holidays);?>
                <script type="text/javascript">
                    function changeState(){
                        if($("#selectAll").is(":checked") == true){
                            $(".selectbox").prop("checked", true);
                        } else {
                            $(".selectbox").prop("checked", false);
                        }

                    }
                </script>

            <?php }?>
            <?php if($step == 8){?>
                <h1 class="first">Stramienen overzetten en uitvoeren.</h1>
                <p>Stramienen die vorig jaar zijn opgezet, kunnen nu worden gekopieeërd. Als je nog een stramien wilt toevoegen, kun je dit na de wizard nog doen. Deze moet je dan wel manueel uitvoeren.</p>
                <?php echo $this->Form->create('Stream', array('url' => '/Admin/endOfYear?step=9'));?>
                <?php echo $this->Stream->copyStreams($streams);?>
                <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth'));?>
                <?php echo $this->Form->end();?>

            <?php }?>
            <?php if($step == 10){?>
                <h1 class="first">Wizard voltooid</h1>
                <p>De Wizard is met succes voltooid. Het enige wat nu nog moet gebeuren, is de website terug open stellen voor gebruik. Dit kan door op de onderstaande knop te drukken.</p>
                <p>Tijdens de procedure zijn er twee database-backups gebeurd. De eerste is gemaakt voor het beginnen van de Wizard, de tweede bij het voltooien. Om deze backups terug te zetten,
                ga je best naar Marc Portier.</p>
                <a href="<?php echo $this->base;?>/Admin/endOfYear?step=11" class="btn btn-primary fullwidth">Applicatie openen voor gebruik</a>
            <?php }?>
        <?php }?>
    </div>
</div>
