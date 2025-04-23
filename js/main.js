
$(document).ready(function(){
    $('.fa-bars').click(function(){
        $('.nav_bar').toggleClass('open');
        $(this).toggleClass('fa-times');
    });
});