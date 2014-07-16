if(!empty($rangeDB)){
    foreach($rangeDB as $calendarDayRecord){
        foreach($range as $calendarDayKey => $calendarDay){
            var_dump($calendarDayKey);
            $calendarDayId = $calendarDayRecord["CalendarDay"]["id"];
            $calendarDayRecord["CalendarDay"]["calendar_item_type_id"] = $request["Request"]["calendar_item_type_id"];
            $calendarDayRecord["CalendarDay"]["replacement_id"] = $request["Request"]["replacement_id"];
            $calendarDayRecord["CalendarDay"]["auth_item_id"] = $authItemId;
            $calendarDayRecord["CalendarDay"]["request_to_calendar_days_id"] = "1";


            if($calendarDay["day_date"] == $calendarDayRecord["CalendarDay"]["day_date"]){
            if($calendarDay["day_date"] == $request["Request"]["start_date"]){
                if($request["Request"]["start_time"] == 'PM'){
                    if($calendarDayRecord["CalendarDay"]["day_time"] == 'PM'){
                        $calendarDay["PM"] = false;
                        $this->CalendarDay->id = $calendarDayId;
                        $this->CalendarDay->save($calendarDayRecord["CalendarDay"]);
                        }
                    } elseif($request["Request"]["end_time"] == 'AM') {
                    if($calendarDayRecord["CalendarDay"]["day_time"] == 'AM'){
                        $calendarDay["AM"] = false;
                        $this->CalendarDay->id = $calendarDayId;
                        $this->CalendarDay->save($calendarDayRecord["CalendarDay"]);
                    }
                } else {
                    $this->CalendarDay->id = $calendarDayId;
                    $this->CalendarDay->save($calendarDayRecord["CalendarDay"]);
                    unset($range[$calendarDayKey]);
                }
        }
        echo '<pre>';
                                        var_dump($calendarDayRecord);
                                        echo '</pre>';

//Create request to calendar day item


        //Update CalendarDay
        $requestToCalendarItemId = $this->RequestToCalendarDay->getLastInsertId();
        $calendarDayRecord["CalendarDay"]["request_to_calendar_days_id"] = $requestToCalendarItemId;
        $this->CalendarDay->save($calendarDayRecord["CalendarDay"]);

        }
        }
    }
}