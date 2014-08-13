<div class="week">
    <h2>Weeknr</h2>
    <div class="spacer"></div>
        <table class="week">
            <?php
                $thismonth = date('Y-m');
                $firstmonday = date('d-m-Y', strtotime('first monday of ' . $thismonth));
                echo $firstmonday;
            ?>
            <tr class="titlerow"><th>Maandag</th><th>Dinsdag</th><th>Woensdag</th><th>Donderdag</th><th>Vrijdag</th></tr>
            <tr class="am"><td></td><td></td><td></td><td></td><td></td></tr>
            <tr class="pm"><td></td><td></td><td></td><td></td><td></td></tr>
        </table>


</div>