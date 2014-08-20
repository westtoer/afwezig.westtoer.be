function updateSecondWeek(id){
    if($('#assymetry-' + id).text() == 'Tweewekelijks'){
        var all = $(".weekOne").map(function() {
            var element = $(this).attr('id');
            var elementItems = element.split('-');
            $('#' + elementItems[0] + '-' + (parseFloat(elementItems[1])+5) + '-' + elementItems[2]).val($('#' + element).val());
        }).get();
    }
}

function addStream(subhtml){

    var weeks = ["weekOne", "weekTwo"];
    var hours = ["AM", "PM"];
    var elements = {'weekOne' : ['monday-1',  'tuesday-2',  'wednesday-3',  'thursday-4',  'friday-5'], 'weekTwo': ['monday-6', 'tuesday-7', 'wednesday-8', 'thursday-9', 'friday-10']}
    var employee = $('#employee').val();
    var employeeText = $("#employee option[value='" + employee + "']").text();

    var html ='';

    html += '<hr /><div class="row"><div class="col-md-9"><h2 class="first">' + employeeText + '</h2></div><div class="col-md-3"><a id="assymetry-' + employee + '"Onclick="toggleAssymetric(' + employee +')" class="btn btn-primary fullwidth">Tweewekelijks</a></div></div>'

    $.each(weeks, function(i, week){
        html += '<div class="week"><table class="table week">';
        html += '<tr><th></th><th width="20%">Maandag</th><th width="20%">Dinsdag</th><th width="20%">Woensdag</th><th width="20%">Donderdag</th><th width="20%">Vrijdag</th></tr>';
        $.each(hours, function(h, hour){
            html += '<tr class="' + hour.toLocaleLowerCase()  + '"><td>' + hour.toUpperCase()  +'</td>';
            $.each(elements[week], function(e, element){
                html += '<td>';
                html += '<select id="' + element + '-' + hour.toUpperCase() + '-' + employee +'" name="data';
                html += '[' + employee + ']';
                html += '[Stream][elements][' + element.charAt(0).toUpperCase() + element.slice(1) + '-' + hour.toUpperCase() + ']" class="form-control ' + week;
                if(week == 'weekOne'){
                    html += '" OnChange="updateSecondWeek(' + employee + ')">';
                } else{
                    html += ' ' + employee + '">';
                }
                html += subhtml;
                html += '</select></td>';
            });
            html += '</tr>';
        });
        html += '</table>';
        html += '</div>';
    });

    if(employee != '-1'){
        $('#wrapper-streams').append(html);
    } else {
        alert('Je moet een geldige gebruiker opgeven.');
    }

    var all = $("." + employee).map(function() {
        $('#' + $(this).attr('id')).attr('disabled', true);
    }).get();

    $("#employee option[value='" + employee + "']").remove();
}

function toggleAssymetric(id){
    if($('#assymetry-' + id).text() == 'Tweewekelijks'){
            var all = $(".weekTwo." + id).map(function() {
                $('#' + $(this).attr('id')).attr('disabled', false);
            }).get();

        $('#assymetry-' + id).html('Wekelijks');
        $('#assymetry-' + id).removeClass('btn-primary');
        $('#assymetry-' + id).addClass('btn-warning');
    } else {
        $(document).ready(function(){
            var all = $(".weekTwo."+id).map(function() {
                $('#' + $(this).attr('id')).attr('disabled', true);
            }).get();
        })

        updateSecondWeek();

        $('#assymetry-' + id).html('Tweewekelijks');
        $('#assymetry-' + id).removeClass('btn-warning');
        $('#assymetry-' + id).addClass('btn-primary');
    }
}