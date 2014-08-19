<?php
if(isset($this->request->query["month"])){
    //input the export file name
    $this->xls->setHeader('Schaubroeck_export_'.date('Y_m_d'));
    $this->xls->addXmlHeader();
    $this->xls->setWorkSheetName('Data');

        if(isset($this->request->query["type"])){
            $exportElements = array();
            $this->xls->openRow();
            $this->xls->writeString('westtoernummer');
            $this->xls->writeString('NummerPersoneelslidSchaubroeck');
            $this->xls->writeString('Datum');
            $this->xls->writeString('aard_schaubroeck');
            $this->xls->writeString('code_schaubroeck');
            $this->xls->writeString('extensie_schaubroeck');
            $this->xls->writeString('uur');
            $this->xls->writeString('totaal');
            $this->xls->closeRow();

            foreach($daysFull as $date => $days){
               foreach($days as $employee => $day){
                   if($day[0]["type"] == $day[1]["type"]){
                       $value = 7.5999999;
                       $type = $day[0]["type"];
                       $this->xls->openRow();
                       $this->xls->writeString(752);
                       $this->xls->writeString(explode('/', $employee)[2]);
                       $this->xls->writeString($date);
                       if($type != 'ZO' and $type != 'ZA'){
                           echo $type;
                           $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["aard_schaubroek"]);
                           $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["code_schaubroek"]);
                           $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["ext_schaubroek"]);
                       } else {
                           $this->xls->writeString("Weekend");
                           $this->xls->writeString("Weekend");
                           $this->xls->writeString("Weekend");
                       }

                       $this->xls->writeString($value);
                       if($type != 'ZO' and $type != 'ZA'){
                           $this->xls->writeString(752 . ';' . explode('/', $employee)[2] . ';' . $date . ';' . $calendarTypes[$type]["CalendarItemType"]["aard_schaubroek"] . ';' . $calendarTypes[$type]["CalendarItemType"]["code_schaubroek"] . ';' . $calendarTypes[$type]["CalendarItemType"]["ext_schaubroek"] . ';' . $value);
                       } else {
                           $this->xls->writeString("Weekend");
                       }
                       $this->xls->closeRow();
                   } else {
                        foreach($day as $dateObject){
                            $value = 3.7999999;
                            $type = $dateObject["type"];
                            $this->xls->openRow();
                            $this->xls->writeString(752);
                            $this->xls->writeString(explode('/', $employee)[2]);
                            $this->xls->writeString($date);
                            $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["aard_schaubroek"]);
                            $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["code_schaubroek"]);
                            $this->xls->writeString($calendarTypes[$type]["CalendarItemType"]["ext_schaubroek"]);
                            $this->xls->writeString($value);
                            $this->xls->writeString(752 . ';' . explode('/', $employee)[2] . ';' . $date . ';' . $calendarTypes[$type]["CalendarItemType"]["aard_schaubroek"] . ';' . $calendarTypes[$type]["CalendarItemType"]["code_schaubroek"] . ';' . $calendarTypes[$type]["CalendarItemType"]["ext_schaubroek"] . ';' . $value);
                            $this->xls->closeRow();
                        }
                   }


               }
            }

            $this->xls->addXmlFooter();
            exit();

        } else {
            if(isset($this->request->query["webview"])){
                echo '<div class="scrollblock">';
                echo $this->Admin->webview($data);
                echo '</div>';
            } else {
                //1st row for columns name
                $this->xls->openRow();
                $this->xls->writeString('Naam');
                $this->xls->writeString('Voornaam');

                foreach($dateRange as $date){
                    if(explode('/', $date)[1] == "AM"){
                        $exportDate = explode('/', $date)[0] . ' 6:00:00';
                    } else {
                        $exportDate = explode('/', $date)[0] . ' 12:00:00';
                    }
                    $this->xls->writeString($exportDate);
                }

                $this->xls->closeRow();

                foreach($data as $employee => $days){

                    $this->xls->openRow();
                    $this->xls->writeString(explode(' ', $employee)[1]);
                    $this->xls->writeString(explode(' ', $employee)[0]);

                    foreach($days as $date => $type){
                        if(count($type) == 2){
                            $type = $type[1];
                        }

                        $this->xls->writeString($type);

                    }

                    $this->xls->closeRow();
                }
                $this->xls->addXmlFooter();
                exit();
            }

        }


} else {?>

    <div class="row">
        <div class="col-md-3">
            <?php echo $this->element('admin/base_admin_menu');?>
        </div>
        <div class="col-md-9">
            <h2 class="first">Een export opstellen</h2>
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-primary fullwidth spaced" href="<?php echo $this->here;?>?month=<?php echo date('m');?>">Genereer standaardexport</a>
                    <a class="btn btn-success fullwidth spaced" href="<?php echo $this->here;?>?month=<?php echo date('m');?>&webview=1">Bekijk dit in de webview</a>
                </div>
                <div class="col-md-6">
                    <h4 class="first">Standaardexport</h4>
                    <p>Deze export is de standaardexport, waar iedereen wordt opgelijst van de eerste van de maand tot de laatste van de maand.</p>
                </div>
            </div><hr />
            <div class="row">
                <div class="col-md-6">
                    <input type="number" class="form-control spaced" placeholder="Dag van de maand" id="daynumber-2">
                    <a class="btn btn-primary fullwidth spaced" onClick="exportDay(2)">Genereer gelimiteerde export</a>
                    <a class="btn btn-success fullwidth spaced" onClick="exportDay(2, 'webview')">Bekijk dit in de webview</a>
                </div>
                <div class="col-md-6">
                    <h4 class="first">Gelimiteerde export</h4>
                    <p>Deze export dient om wijzigingen door te geven die na een bepaalde dag vallen.</p>
                </div>
            </div><hr />
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-primary fullwidth" href="<?php echo $this->here;?>?month=<?php echo date('m');?>&type=1">Genereer Schaubroeck export</a></div>
                <div class="col-md-6">
                    <h4 class="first">Schaubroeck export</h4>
                    <p>Deze export doet net hetzelfde als de standaardexport, maar dan voor het toekomstig Schaubroeck-systeem.</p>
                </div>
            </div><hr />
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 formspaced-left">
                            <input type="number" class="form-control spaced" placeholder="Dag van de maand" id="daynumber-4">
                        </div>
                        <div class="col-md-6 formspaced-right">
                            <select class="form-control spaced" id="month-4">
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
                    </div>
                    <a class="btn btn-primary fullwidth" onClick="exportMonth(4)">Genereer specifieke export</a></div>
                <div class="col-md-6">
                    <h4 class="first">Andere maand</h4>
                    <p>Exporteer een voorgaande maand, al dan niet met een afwijkende startdatum.</p>
                </div>
            </div><hr />
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 formspaced-left">
                            <input type="number" class="form-control spaced" placeholder="Dag van de maand" id="daynumber-5">
                        </div>
                        <div class="col-md-6 formspaced-right">
                            <select class="form-control spaced" id="month-5">
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
                    </div>
                    <a class="btn btn-primary fullwidth" onClick="exportMonthSchaubroeck(5)">Genereer specifieke export</a></div>
                <div class="col-md-6">
                    <h4 class="first">Andere maand Schaubroeck</h4>
                    <p>Deze export doet net hetzelfde als Andere maand, maar dan voor het toekomstig Schaubroeck-systeem.</p>
                </div>
            </div><hr />
        </div>
    </div>

    <script>
        function exportDay(rownr, webview){
            var webview = webview || null;
            var daynr = $('#daynumber-' + rownr).val();
            if(daynr != null){
                if(webview != null){
                    window.location.href = "<?php echo $this->here;?>?month=<?php echo date('m');?>&limit=" + daynr + "&webview=1";
                } else {
                    window.location.href = "<?php echo $this->here;?>?month=<?php echo date('m');?>&limit=" + daynr;
                }
            }
        }

        function exportMonth(rownr){
            var monthnr = $('#month-' + rownr).val();
            var daynr = $('#daynumber-' + rownr).val();
            if(daynr == ''){
                daynr = 1;
            }
            if(monthnr !== null){
                window.location.href = "<?php echo $this->here;?>?month=" + monthnr + "&limit=" + daynr;
            }
        }

        function exportMonthSchaubroeck(rownr){
            var monthnr = $('#month-' + rownr).val();
            var daynr = $('#daynumber-' + rownr).val();
            if(daynr == ''){
                daynr = 1;
            }
            if(monthnr !== null){
                window.location.href = "<?php echo $this->here;?>?month=" + monthnr + "&limit=" + daynr + '&type=1';
            }
        }
    </script>
<?php }?>

