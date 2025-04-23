
$(document).ready(function(){


    $('.fa-bars').click(function(){
        $('.nav_bar').toggleClass('open');
        $(this).toggleClass('fa-times');
    });

    $(window).on('scroll load',function(){
        $('.nav_bar').removeClass('open');
        $('.fa-bars').removeClass('fa-times');

        if($(window).scrollTop() > 20){
            $('header').addClass('header-active')
        }else{
            $('header').removeClass('header-active')
        }
    });
});

