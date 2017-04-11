$(document).ready(function(){
    "use strict";
	/*
	 ==============================================================
	 Drop Down Toggle
	 ==============================================================
	 */
    if($(".dropdown-toggle").length){
        $('.dropdown-toggle').dropdown()

    }
	/*
	 ==============================================================
	 Select Menu
	 ==============================================================
	 */

    var selector = $("select"),
        selectMenu = $("#select-menu");

    if (selectMenu.length){
        selectMenu.selectbox();
    }

    if (selector.length){
        selector.selectric();
    }
	/*
	 ==============================================================
	 Toggle
	 ==============================================================
	 */
    $( ".show2" ).on('click',function() {
        $( ".cart-form" ).slideToggle( "slow", function() {
            // Animation complete.
        });
    });
    $( ".show" ).on('click',function() {
        $( ".categories-ul" ).slideToggle( "slow", function() {
            // Animation complete.
        });
    });
	/*
	 ==============================================================
	 Owl Tab Slider  Script Start
	 ==============================================================
	 */

    var owl = $("#tabs-slider, #tabs-slider2");
    if (owl.length){
        owl.owlCarousel({
            items: 4,
            lazyLoad: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            loop: true,
            responsive:{
                0: {items:1},
                450: {items:1},
                600: {items:2},
                700: {items:4},
                1000: {items:4},
                1200: {items:4}
            }
        });
    }
	/*
	 ==============================================================
	 Back to Top  Script Start
	 ==============================================================
	 */
    $(window).scroll(function () {
        if ($(this).scrollTop() > 400) {
            $('.go-up').fadeIn();
        } else {
            $('.go-up').fadeOut();
        }
    });
    $('.go-up').on('click', function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
	/*
	 ==============================================================
	 DL Responsive Menu
	 ==============================================================
	 */
    if(typeof($.fn.dlmenu) == 'function'){
        $('#kode-responsive-navigation').each(function(){
            $(this).find('.dl-submenu').each(function(){
                if( $(this).siblings('a').attr('href') && $(this).siblings('a').attr('href') != '#' ){
                    var parent_nav = $('<li class="menu-item kode-parent-menu"></li>');
                    parent_nav.append($(this).siblings('a').clone());

                    $(this).prepend(parent_nav);
                }
            });
            $(this).dlmenu();
        });
    }
});