$(document).ready(function(){
    $('#hidden').css('display', 'none');
});

$(document).keypress((function(e) {
    var pass = "jedi";
    var typed = "";

    return function(e) {
        typed += String.fromCharCode(e.which);

        console.log(typed);
        if (typed === pass) {
            alert('Do or do not, there is not try');
            $('#hidden').css('display', 'block');
            console.log('Fired');
        }
    };
})());