<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="well flat">
            <h2>Stramien toepassen</h2>
            <h4>Vanaf een datum</h4>
            <div class="row">
                <div class="col-md-8 formspaced-left">
                    <input class="form-control" name="data[Request][start_date]" placeholder="Start" value="" id="applyDate" type="text">
                </div>
                <div class="col-md-4 formspaced-right">
                    <a onClick="goToDate()" class="btn btn-success fullwidth">Vanaf deze datum</a>
                </div>
            </div>

            <hr>
            <h4>Nu toepassen</h4>
            <div class="row">
                <div class="col-md-8 formspaced-left">
                    <p>Stramien toepassen vanaf <?php echo date('Y-m-d');?> tot <?php echo date('Y-m-d', strtotime(date('Y') . '-12-31'));?> voor <?php echo $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"];?>.</p>
                </div>
                <div class="col-md-4 formspaced-right">
                    <a href="<?php echo $this->here;?>?apply=1" class="btn btn-success fullwidth spaced">Vanaf nu</a>
                </div>
            </div>
            <a href="/Admin/viewStreams" class="btn btn-danger fullwidth spaced">Keer terug</a>

        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(function(){
        $.datepicker.setDefaults(
            $.extend($.datepicker.regional['nl'])
        );
        $('#applyDate').datepicker({
            minDate: '-0',
            dateFormat: 'yy-mm-dd',
            closeText: 'Sluiten',
            prevText: '←',
            nextText: '→',
            currentText: 'Vandaag',
            monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
            monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
                'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
            dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
            dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
            dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
            weekHeader: 'Wk'
        });
    });

    function goToDate(){
        var date = $('#applyDate').val();

        if(date != null){
            window.location.href = '<?php echo $this->here;?>?apply=1&date=' + date;
        } else {
            alert('Je moet een geldige datum opgeven.');
        }

    }
</script>