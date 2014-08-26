<?php
class CalendarItemTypeHelper extends AppHelper{

    public function selectorAllCalendarItemTypes($calendaritemtypes, $datatype = 'html'){
        //Give a standard null option
        if($datatype == 'html'){
            $CITOptions[] = '<option value="9">Gewerkt</option>';
            foreach($calendaritemtypes as $calendaritemtype){
                if($calendaritemtype["CalendarItemType"]["id"] != 9){
                    $CITOptions[] = '<option value="' .$calendaritemtype["CalendarItemType"]["id"] . '">' . $calendaritemtype["CalendarItemType"]["name"] . '</option>';
                }
            }
        }

        //Fill with all CalendarItemTypes
        if($datatype == 'mixed'){
            foreach($calendaritemtypes as $calendaritemtype){
                if($calendaritemtype["CalendarItemType"]["id"] != 9){
                    $CITOptions[] = array('id' => $calendaritemtype["CalendarItemType"]["id"], 'html' =>'<option value="' .$calendaritemtype["CalendarItemType"]["id"] . '">' . $calendaritemtype["CalendarItemType"]["name"] . '</option>');
                }
            }
        }


        return $CITOptions;
    }

}