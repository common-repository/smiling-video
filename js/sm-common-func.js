function callAjaxAsync(urlStr){
var xmlhttp;
xmlhttp = new XMLHttpRequest();
xmlhttp.open("GET", urlStr, true);
xmlhttp.send();
}
function sm_stripTitle(str){return str.replace(/<!--.*?-->/g,'').replace(/\r?\n/g, '');}
function httpGetSync(urlStr){
var xmlHttp = new XMLHttpRequest();
xmlHttp.open("GET",urlStr, false );
xmlHttp.send( null );
return xmlHttp.responseText;
}
