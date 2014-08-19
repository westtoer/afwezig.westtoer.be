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