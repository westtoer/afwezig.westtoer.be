<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
        <div class="well flat">
            <h3 class="first">Rapporten genereren</h3>
            <p><small>Om een rapport te genereren, vink je één enkele medewerker af, kies je het rapporttype en klik je op één van de knoppen</small></p>
            <select class="form-control spaced" id="range">
                <option value="0">Voor het jaar <?php echo date('Y');?></option>
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
            <a href="#" onClick="show('all')" class="btn btn-primary fullwidth spaced">Toon volledig rapport</a>
            <a href="#" onClick="show('off')" class="btn btn-primary fullwidth spaced">Toon enkel afwezigheden</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="first">Medewerkers</h2>
        <div class="well flat">
            <div class="row">
                <div class="col-md-8">
                    Nieuwe werknemers toevoegen kan je doen met het formulier in het administratiepaneel. Gebruikers kunnen zelf geen werknemers toevoegen.
                </div>
                <div class="col-md-4">
                    <a href="<?php echo $this->base;?>/Admin/registerEmployee" class="btn btn-success fullwidth">Nieuwe werknemer toevoegen</a>
                </div>
            </div>
        </div>
        <?php echo $this->Employee->tableEmployees($employees, "filtertable");?></div>
</div>
<script language="javascript" type="text/javascript">
    setFilterGrid("filtertable");
</script>
<script>


    function show(type){
        var type = type || 'all';
        var option = $('#range').val();
        var count = 0;
        var error = false;
        var selectedEmployee = 0;
        $(".employeesSelector:checked").each(function() {
            if(count > 0){
                alert('Je mag maar één medewerker selecteren om een rapport te genereren.');
                error = true;
            } else {
                selectedEmployee = $(this).data('employee-id');
                count++;
            }
        });

        if(type == 'off'){
            var typeSet = ('&type=off');
        } else {
            var typeSet = '';
        }

        if(error == false){
            if(option == 0){
                window.location.href = '<?php echo $this->base;?>/Admin/generateReportEmployee/' + selectedEmployee + '?' + typeSet;
            } else {
                window.location.href = '<?php echo $this->base;?>/Admin/generateReportEmployee/' + selectedEmployee + '?' + '&month=' + option + typeSet;
            }
        }
    }
</script>

