jQuery(document).ready(function($) {  
    $('tr.header').nextUntil('tr.header').slideToggle(100);
    $('tr.header').click(function(){
        $(this).find('span').text(function(_, value){return value=='\u25BC '?'\u25BA ':'\u25BC '});
        $(this).nextUntil('tr.header').slideToggle(100, function(){
        });
    });
 });