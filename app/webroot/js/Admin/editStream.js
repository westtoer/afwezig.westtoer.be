var json = json;



var assymetric = 0
function updateSecondWeek(){
    if(assymetric == 0){
        var all = $(".weekOne").map(function() {
            var element = $(this).attr('id');
            var elementItems = element.split('-');
            $('#' + elementItems[0] + '-' + (parseFloat(elementItems[1])+5) + '-' + elementItems[2]).val($('#' + element).val());
        }).get();
    }
}

$(document).ready(function(){
    var all = $(".weekTwo").map(function() {
        $('#' + $(this).attr('id')).attr('disabled', true);
    }).get();

    if(json != null){

        $.each( json, function(i, obj) {
            console.log($("#" + obj.element+" option[value='" + obj.calendar_item_type_id + "']").attr('selected', 'selected'));
        });
    } else {

    }

})

function toggleAssymetric(){
    if(assymetric == 0){
        assymetric = 1;
        $(document).ready(function(){
            var all = $(".weekTwo").map(function() {
                $('#' + $(this).attr('id')).attr('disabled', false);
            }).get();
        })

        $('#assymetry').html('Wekelijks');
        $('#assymetry').removeClass('btn-primary');
        $('#assymetry').addClass('btn-warning');
    } else {
        assymetric = 0;
        $(document).ready(function(){
            var all = $(".weekTwo").map(function() {
                $('#' + $(this).attr('id')).attr('disabled', true);
            }).get();
        })

        updateSecondWeek();

        $('#assymetry').html('Tweewekelijks');
        $('#assymetry').removeClass('btn-warning');
        $('#assymetry').addClass('btn-primary');
    }
}
