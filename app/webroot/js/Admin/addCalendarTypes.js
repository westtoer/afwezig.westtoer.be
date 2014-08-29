var count = 0;

$(document).ready(function(){

});

function newType(){
    var html = '';
    html += '<tr>';
    html += '<td><input type="text" name="data[new][' + count + '][CalendarItemType][name]" class="form-control"></td>';
    html += '<td><input type="text" name="data[new][' + count + '][CalendarItemType][code]" class="form-control"></td>';
    html += '<td><select name="data[new][' + count + '][CalendarItemType][user_allowed]" class="form-control"><option value="0">Nee</option><option value="1">Ja</option></select></td>';
    html += '<td><select name="data[new][' + count + '][CalendarItemType][dinner_cheque]" class="form-control"><option value="0">Nee</option><option value="1">Ja</option></select></td>';
    html += '<td><input type="text" name="data[new][' + count + '][CalendarItemType][code_schaubroek]" class="form-control"></td>';
    html += '<td><input type="text" name="data[new][' + count + '][CalendarItemType][aard_schaubroek]" class="form-control"></td>';
    html += '<td><input type="text" name="data[new][' + count + '][CalendarItemType][ext_schaubroek]" class="form-control"></td>';
    $('.table tr:last').after(html);
    count++;
}
