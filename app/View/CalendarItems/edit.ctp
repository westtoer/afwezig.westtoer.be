<div class="row">
    <div class="col-md-6">
        <div class="well flat">
            <h3 class="first centerpiece">Oud</h3>
            <div class="row">
                <div class="col-md-6">
                    <h4><?php echo $CalendarItem["Employee"]["name"] . ' ' . $CalendarItem["Employee"]["surname"];?></h4>
                    <p><?php echo $CalendarItem["CalendarItemType"]["name"];?>: <?php echo $CalendarItem["CalendarItem"]["note"];?><br />
                    Start: <?php echo $CalendarItem["CalendarItem"]["start_date"] . ' ' . $CalendarItem["CalendarItem"]["start_time"];?><br />
                    Einde: <?php echo $CalendarItem["CalendarItem"]["end_date"] . ' ' . $CalendarItem["CalendarItem"]["end_time"];?></p>
                </div>
                <div class="col-md-6">
                    <h4>Vervanging</h4>
                    <p>
                        <?php
                        $replacement = $this->CalendarItem->replacementToName($CalendarItem["CalendarItem"]["replacement_id"], $employees);
                        echo '<a href="' . $this->base . '/employees/view/'.$replacement["Employee"]["id"] . '">' .$replacement["Employee"]["name"] . ' ' . $replacement["Employee"]["surname"] .'</a>';
                        ;?>
                    </p>

                    <b>Goedgekeurd?</b>
                    <p><?php echo $this->CalendarItem->isApproved($CalendarItem["CalendarItem"]["approved"]);?></p>
                    <b>Vakantiewaarde</b>
                    <p><?php echo $CalendarItem["CalendarItem"]["calculatedDays"];?> dagen</p>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-6">
        <div class="well flat"><h3 class="first centerpiece">Nieuw</h3></div>
    </div>
</div>