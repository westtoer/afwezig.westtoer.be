function weekcalculate(weekvalue){
    console.log('fired');
    console.log(weekvalue);
    var sanitisedweek = weekvalue.split('-');
    sanitisedweek[1] = sanitisedweek[1].substring(1,3)
    var valueWeek = sanitisedweek[1];
    var valueYear = sanitisedweek[0];
    console.log(valueWeek + ' ' + valueYear);
    var start = new Date(firstDayOfWeek(valueWeek,valueYear));
    var formattedstart = start.getDate() + '-' + start.getMonth() + '-' + start.getFullYear();
    var stop = start.addDays(6);
    var formattedstop = stop.getDate() + '-' + stop.getMonth() + '-' + stop.getFullYear();
    var splitdates = [formattedstart, formattedstop];
    return splitdates;


}

function firstDayOfWeek(week, year) {

    if (year==null) {
        year = (new Date()).getFullYear();
    }

    var date       = firstWeekOfYear(year),
        weekTime   = weeksToMilliseconds(week),
        targetTime = date.getTime() + weekTime;

    return date.setTime(targetTime);

}

function weeksToMilliseconds(weeks) {
    return 1000 * 60 * 60 * 24 * 7 * (weeks - 1);
}

function firstWeekOfYear(year) {
    var date = new Date();
    date = firstDayOfYear(date,year);
    date = firstWeekday(date);
    return date;
}

function firstDayOfYear(date, year) {
    date.setYear(year);
    date.setDate(1);
    date.setMonth(0);
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);
    return date;
}

/**
 * Sets the given date as the first day of week of the first week of year.
 */
function firstWeekday(firstOfJanuaryDate) {
    // 0 correspond au dimanche et 6 correspond au samedi.
    var FIRST_DAY_OF_WEEK = 1; // Monday, according to iso8601
    var WEEK_LENGTH = 7; // 7 days per week
    var day = firstOfJanuaryDate.getDay();
    day = (day === 0) ? 7 : day; // make the days monday-sunday equals to 1-7 instead of 0-6
    var dayOffset=-day+FIRST_DAY_OF_WEEK; // dayOffset will correct the date in order to get a Monday
    if (WEEK_LENGTH-day+1<4) {
        // the current week has not the minimum 4 days required by iso 8601 => add one week
        dayOffset += WEEK_LENGTH;
    }
    return new Date(firstOfJanuaryDate.getTime()+dayOffset*24*60*60*1000);
}

function assertDateEquals(effectiveDate, expectedDate, description) {
    if ((effectiveDate==null ^ expectedDate==null) || effectiveDate.getTime()!=expectedDate.getTime()) {
        console.log("assert failed: "+description+"; effective="+effectiveDate+", expected="+expectedDate);
    }
}
function assertEquals(effectiveValue, expectedValue, description) {
    if (effectiveValue!=expectedValue) {
        console.log("assert failed: "+description+"; effective="+effectiveValue+", expected="+expectedValue);
    }
}

// expect the first day of year to be a monday
for (var i=1970; i<2050; i++) {
    assertEquals(firstWeekOfYear(i).getDay(), 1, "first day of year "+i+" must be a monday"); // 1=Monday
}

// assert some future first day of first week of year; source: http://www.epochconverter.com/date-and-time/weeknumbers-by-year.php
assertDateEquals(firstWeekOfYear(2013), new Date(Date.parse("Dec 31, 2012")), "2013");
assertDateEquals(firstWeekOfYear(2014), new Date(Date.parse("Dec 30, 2013")), "2014");
assertDateEquals(firstWeekOfYear(2015), new Date(Date.parse("Dec 29, 2014")), "2015");
assertDateEquals(firstWeekOfYear(2016), new Date(Date.parse("Jan 4, 2016")), "2016");
assertDateEquals(firstWeekOfYear(2017), new Date(Date.parse("Jan 2, 2017")), "2017");
assertDateEquals(firstWeekOfYear(2018), new Date(Date.parse("Jan 1, 2018")), "2018");
assertDateEquals(firstWeekOfYear(2019), new Date(Date.parse("Dec 31, 2018")), "2019");
assertDateEquals(firstWeekOfYear(2020), new Date(Date.parse("Dec 30, 2019")), "2020");
assertDateEquals(firstWeekOfYear(2021), new Date(Date.parse("Jan 4, 2021")), "2021");
assertDateEquals(firstWeekOfYear(2022), new Date(Date.parse("Jan 3, 2022")), "2022");
assertDateEquals(firstWeekOfYear(2023), new Date(Date.parse("Jan 2, 2023")), "2023");
assertDateEquals(firstWeekOfYear(2024), new Date(Date.parse("Jan 1, 2024")), "2024");
assertDateEquals(firstWeekOfYear(2025), new Date(Date.parse("Dec 30, 2024")), "2025");
assertDateEquals(firstWeekOfYear(2026), new Date(Date.parse("Dec 29, 2025")), "2026");

console.log("All assertions done.");

Date.prototype.addDays = function(days) {
    this.setDate(this.getDate() + days);
    return this;
};