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
    if($("#select-menu").length){
        $("#select-menu").selectbox();
    }
    if($("select").length){
        $('select').selectric();
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
	 Accordian Script Start
	 ==============================================================
	 */

    if($('.accordion').length){
        //custom animation for open/close
        $.fn.slideFadeToggle = function(speed, easing, callback) {
            return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
        };

        $('.accordion').accordion({
            defaultOpen: 'section1',
            cookieName: 'nav',
            speed: 'slow',
            animateOpen: function (elem, opts) { //replace the standard slideUp with custom function
                elem.next().stop(true, true).slideFadeToggle(opts.speed);
            },
            animateClose: function (elem, opts) { //replace the standard slideDown with custom function
                elem.next().stop(true, true).slideFadeToggle(opts.speed);
            }
        });
    }
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
	/*
	 ==============================================================
	 Toll Tip  Script Start
	 ==============================================================
	 */
    $('[data-toggle="tooltip"]').tooltip()

	/*
	 =======================================================================
	 Range Slider Script Script
	 =======================================================================
	 */
    if($('.slider-range').length){
        $( ".slider-range" ).slider({
            range: true,
            min: 0,
            max: 500,
            values: [ 50, 450 ],
            slide: function( event, ui ) {
                $( ".amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
            }
        });
        $( ".amount" ).val( "$" + $( ".slider-range" ).slider( "values", 0 ) + " - $" + $( ".slider-range" ).slider( "values", 1 ) );
    }

	/*
	 ==============================================================
	 Toggle page view on book grid
	 ==============================================================
	 */
    var mainContent = $('.main-content');

    mainContent.on('click', '.pageview__list', function (e) {
        e.preventDefault();
        var query = $('#search-query').val(),
            page = $('#page').val(),
            sendData = {view: 'list', 'query': query, 'page': page};

        mainContent.append('<span class="main-content__loading"></span>');
        mainContent.css('opacity', 0.5);

        $.get($(this).prop('href'), sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
            }
        });
    });

    mainContent.on('click', '.pageview__column', function (e) {
        e.preventDefault();
        var query = $('#search-query').val(),
            page = $('#page').val(),
            sendData = {view: 'column', 'query': query, 'page': page};

        mainContent.append('<span class="main-content__loading"></span>');
        mainContent.css('opacity', 0.5);

        $.get($(this).prop('href'), sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
            }
        });
    });

    mainContent.on('click', '.pageview__grid', function (e) {
        e.preventDefault();
        var query = $('#search-query').val(),
            page = $('#page').val(),
            sendData = {view: 'grid', 'query': query, 'page': page};

        mainContent.append('<span class="main-content__loading"></span>');
        mainContent.css('opacity', 0.5);

        $.get($(this).prop('href'), sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
            }
        });
    });
	/*
	 ==============================================================
	 Pagination
	 ==============================================================
	 */
    mainContent.on('click', 'a.pagination-link', function (e) {
        e.preventDefault();
        var query = $('#search-query').val(),
            page  = $(this).data('page'),
            sendData = {'query': query, 'page': page},
            newLink = $(this).prop('href');

        if ($(this).hasClass('active')) {
            return;
        }

        mainContent.append('<span class="main-content__loading"></span>');
        mainContent.css('opacity', 0.5);

        $.get(newLink, sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
                history.pushState(null, null, newLink);
            }
        });
    });
});