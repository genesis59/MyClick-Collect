$(document).ready( function(){
    var cat;
    if(getParameter('cat') == undefined){
        cat = 1;
    } else {
        cat = getParameter('cat');
    }   
    $('a.cat' + cat).removeClass('text-dark').addClass('active text-primary')     
});

function getParameter(p)
{
    var url = window.location.search.substring(1);
    var varUrl = url.split('&');
    for (var i = 0; i < varUrl.length; i++)
    {
        var parameter = varUrl[i].split('=');
        if (parameter[0] == p)
        {
            return parameter[1];
        }
    }
}