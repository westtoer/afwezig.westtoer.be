var inFlowVal = '';
var employeeState = '';

$(document).ready(function(){
    $('#SubmitButton').attr('disabled', 'disabled');
});

function setInFlow(){
    inFlowVal = $('#inflow').val();
    if(inFlowVal == 1){
        $('#RequestDestination').val('false');
    } else {
        $('#RequestDestination').val('true');
    }
}

function isEmployeeSet(){
    employeeState = $('#RequestEmployeeId').val();
    if(employeeState == 0){
        $('#SubmitButton').attr('disabled', 'disabled');
    } else {
        $('#SubmitButton').removeAttr('disabled', 'disabled');
    }
}