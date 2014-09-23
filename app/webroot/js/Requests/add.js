var beginDate = '-0';
var limit = new Date();

limit = limit.getFullYear();
limit += '-01-01';

function onChangeBegin(){
    beginDate = $('#RequestStartDate').val();
    $('#RequestEndDate').datepicker('option','minDate', beginDate);
}

$(function(){
    $.datepicker.setDefaults(
        $.extend($.datepicker.regional['nl'])
    );
    $('#RequestStartDate').datepicker({
        minDate: limit,
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
        weekHeader: 'Wk',
        firstDay: 1
    });
});

$(function(){
    $.datepicker.setDefaults(
        $.extend($.datepicker.regional['nl'])
    );
    $('#RequestEndDate').datepicker({
        minDate: beginDate,
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
        weekHeader: 'Wk',
        firstDay: 1
    });
});

$(document).ready(function(){
        $('#RequestCalendarItemTypeId').val(23);
});

function initiated(){
    var start = $('#RequestStartDate').val();
    var end = $('#RequestEndDate').val();
    if(start.indexOf('-') == 5 && end.indexOf('-') == 5){
        $("#RequestAddForm").submit();
        $("#RequestSubmitButton").attr('disabled', 'disabled');
        $("#RequestSubmitButton").html('Bezig met verwerken...');
    } else {
        alert('Your dateformat is wrong');
    }



}