<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een nieuw stramien toevoegen</h2>
        <?php echo $this->Form->create('Stream', array('url' => $this->here));?>
        <!-- Controls -->
        <div class="well flat">
            <div class="row">
                <div class="col-md-6 formspaced-left">
                    <select class="form-control" id="employee_id" name="data[Stream][employee_id]">
                    <?php foreach($this->Employee->selectorAllEmployees($employees, 'html', 1) as $option){
                        echo $option;
                    };?>
                    </select>
                </div>
                <div class="col-md-6 formspaced-right">
                    <a OnClick="toggleAssymetric()" id="assymetry" class="btn btn-primary fullwidth">Tweewekelijks</a>
                </div>

            </div>
        </div>

        <!-- Form -->

        <div class="week">
            <table class="table week">
                <tr><th></th><th width="20%">Maandag</th><th width="20%">Dinsdag</th><th width="20%">Woensdag</th><th width="20%">Donderdag</th><th width="20%">Vrijdag</th></tr>
                <tr class="am">
                    <td>AM</td>
                    <td>
                        <select id="monday-1-AM" name="data[Stream][elements][Monday-1-AM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="tuesday-2-AM" name="data[Stream][elements][Tuesday-2-AM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="wednesday-3-AM" name="data[Stream][elements][Wednesday-3-AM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="thursday-4-AM" name="data[Stream][elements][Thursday-4-AM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="friday-5-AM" name="data[Stream][elements][Friday-5-AM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                </tr>
                <tr class="pm">
                    <td>PM</td>
                    <td>
                        <select id="monday-1-PM" name="data[Stream][elements][Monday-1-PM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="tuesday-2-PM" name="data[Stream][elements][Tuesday-2-PM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="wednesday-3-PM" name="data[Stream][elements][Wednesday-3-PM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="thursday-4-PM" name="data[Stream][elements][Thursday-4-PM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="friday-5-PM" name="data[Stream][elements][Friday-5-PM]" class="form-control weekOne" OnChange="updateSecondWeek()">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <div class="week">
            <table class="table week">
                <tr><th></th><th width="20%">Maandag</th><th width="20%">Dinsdag</th><th width="20%">Woensdag</th><th width="20%">Donderdag</th><th width="20%">Vrijdag</th></tr>
                <tr class="am">
                    <td>AM</td>
                    <td>
                        <select id="monday-6-AM" name="data[Stream][elements][Monday-6-AM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="tuesday-7-AM" name="data[Stream][elements][Tuesday-7-AM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="wednesday-8-AM" name="data[Stream][elements][Wednesday-8-AM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="thursday-9-AM" name="data[Stream][elements][Thursday-9-AM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="friday-10-AM" name="data[Stream][elements][Friday-10-AM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                </tr>
                <tr class="pm">
                    <td>PM</td>
                    <td>
                        <select id="monday-6-PM" name="data[Stream][elements][Monday-6-PM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="tuesday-7-PM" name="data[Stream][elements][Tuesday-7-PM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="wednesday-8-PM" name="data[Stream][elements][Wednesday-8-PM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="thursday-9-PM" name="data[Stream][elements][Thursday-9-PM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                    <td>
                        <select id="friday-10-PM" name="data[Stream][elements][Friday-10-PM]" class="form-control weekTwo">
                            <?php foreach($this->CalendarItemType->selectorAllCalendarItemTypes($calendaritemtypes) as $option){
                                echo $option;
                            };?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php echo $this->Form->Submit('Opslaan', array('class' => 'btn btn-success fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>

<?php echo $this->Html->script('Admin/newStream');?>