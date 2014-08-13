<?php
class CalendarItemTypeHelper extends AppHelper{

    public function selectorAllCalendarItemTypes($calendaritemtypes, $datatype = 'html'){
        //Give a standard null option
        if($datatype == 'html'){

            $CITOptions[] = '<option value="0">vanwege</option>';

            //Fill with all Employees
            foreach($calendaritemtypes as $calendaritemtype){
                $CITOptions[] = '<option value="' .$calendaritemtype["CalendarItemType"]["id"] . '">' . $calendaritemtype["CalendarItemType"]["name"] . '</option>';
            }
        }

        return $CITOptions;
    }

}