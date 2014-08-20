<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php if($crud == true){?>
            <?php echo $this->Form->create('Crud', array('url' => $this->here));?>
            <?php echo $this->Admin->crudCalendarDays($calendarDays);?>
            <?php echo $this->Form->submit('Opslaan', array('class' => 'btn btn-primary fullwidth'));?>
            <?php echo $this->Form->end();?>
        <?php } else {?>
            <h2 class="first">Kalenderdagen wijzigen</h2>
            <p>Als er iets niet zou kloppen in de database, kun je dit manueel overschrijven, zodat de export naar Schaubroeck klopt.</p>
            <div class="well flat">
                <div class="row">
                    <div class="col-md-3 formspaced-left">
                        <select class="form-control" id="employee">
                            <?php foreach($this->Employee->selectorAllEmployees($employees, 'html', 3) as $option){
                                echo $option;
                            };?>
                        </select>
                    </div>
                    <div class="col-md-3 formspaced-left formspaced-right">
                        <select class="form-control " id="month">
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
                    <div class="col-md-3 formspaced-left formspaced-right">
                        <input type="text" class="form-control" placeholder="Jaar (Optioneel)" id="year" value="<?php echo date('Y');?>">
                    </div>
                    <div class="col-md-3 formspaced-right">
                        <a OnClick="goToCrud()" class="btn btn-primary fullwidth">Toon records</a>
                    </div>
                </div>
            </div>

            <script>
                function goToCrud(){
                    var month = $('#month').val();
                    var year = $('#year').val();
                    var employee = $('#employee').val();
                    var url = '<?php echo $this->here;?>';

                    if(employee != 0){
                        url += '?employee=' + employee + '&month=' + month;
                        url += '&year=' + year;

                        window.location.href = url;
                    } else {
                        alert('Je moet een geldige gebruiker kiezen.')
                    }
                }
            </script>
        <?php } ?>
    </div>
</div>