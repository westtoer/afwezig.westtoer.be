
function getQueryVariable(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
}
return(false);
}

function removeQueryVariable(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
    param,
    params_arr = [],
    queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
    params_arr = queryString.split("&");
    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
    param = params_arr[i].split("=")[0];
    if (param === key) {
    params_arr.splice(i, 1);
    }
}
rtn = rtn + "?" + params_arr.join("&");
}
return rtn;
}

function groupselect(){
    var e = document.getElementById("groupselect");
    var group = e.options[e.selectedIndex].value;
    var href = document.location.href;
    if(group !== '0'){
    if(group == 1){
    var baseurl = removeQueryVariable('group', href)
    document.location.href = baseurl.replace('?', '');

    } else {

    if(!~href.indexOf('group')){
    document.location.href = href + '?group=' + group;
    } else {
    var newurl = href.replace(getQueryVariable("group"), group);
    document.location.href = newurl;

    }
}
}


}

function userselect(){
    var e = document.getElementById("userselect");
    var user = e.options[e.selectedIndex].value;
    var href = document.location.href;
    if(user !== '0'){


            if(!~href.indexOf('user')){
                document.location.href = href + '?user=' + user;
            } else {
                var newurl = href.replace(getQueryVariable("user"), user);
                document.location.href = newurl;

            }
        }



}


function update(){
    href = document.location.href;
    var baseurl= removeQueryVariable('group', href)
    var baseurl = removeQueryVariable('user', baseurl)
    var baseurl = removeQueryVariable('range', baseurl)
    var cleanedurl = baseurl.replace('?', '');
    var end = cleanedurl;
    var group = [0];
    var range = [0];
    var user = [0];
    if(document.getElementById('weekselect').value !== ""){
        var x = weekcalculate(document.getElementById('weekselect').value);
        var range = [1, x[0], x[1], document.getElementById('weekselect').value];
        end += '?range=' + range[1] + ';' + range[2] + ';' + range[3];
    }
    if(document.getElementById('groupselect').value != 0){
        var group = [1, document.getElementById('groupselect').value];
        if(range[0] !== 1){
            end += '?group=' + group[1];
        } else {
            end += '&group=' + group[1];
        }
    }
    if(document.getElementById('userselect').value != 0){
        var user = [1, document.getElementById('userselect').value];
        if(group[0] !== 1){
            if(range[0] !== 1){
                end += '?user=' + user[1];
            } else {
                end += '&user=' + user[1];
            }} else{
                end += '&user=' + user[1];
            }
        }

    window.location = end;
}