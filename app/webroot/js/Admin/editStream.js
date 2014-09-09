$(document).ready(function(){
    $.each(prev, function(i, object){
        console.log($('#' + object.element + " option[value='" + object.calendar_item_type+ "']").attr('selected', 'selected'));
    });
});