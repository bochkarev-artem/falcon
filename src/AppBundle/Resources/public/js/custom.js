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
    $('[data-toggle="tooltip"]').tooltip();

	/*
	 =======================================================================
	 Range Slider Script Script
	 =======================================================================
	 */
    // var sliderRange = $('.slider-range');
    //
    // if(sliderRange.length){
    //     sliderRange.slider({
    //         range: true,
    //         min: 0,
    //         max: 500,
    //         values: [ 50, 450 ],
    //         slide: function( event, ui ) {
    //             $( ".amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
    //         }
    //     });
    //     $( ".amount" ).val( "$" + sliderRange.slider( "values", 0 ) + " - $" + sliderRange.slider( "values", 1 ) );
    // }

	/*
	 ==============================================================
	 Toggle page view on book grid
	 ==============================================================
	 */
    var mainContent = $('.main-content');

    mainContent.on('click', '.pageview', function (e) {
        e.preventDefault();
        var query = $('#search-query').val(),
            page = $('#page').val(),
            view = $(this).data('view'),
            sort = $('.selectric-dec option:selected').val(),
            sendData = {view: view, 'query': query, 'page': page, 'sort': sort};

        mainContent.append('<span class="main-content__loading fa fa-spinner fa-spin fa-3x fa-fw"></span>');
        mainContent.css('opacity', 0.5);

        $.get($(this).prop('href'), sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
                $('select').selectric('init');
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
            sort = $('.selectric-dec option:selected').val(),
            sendData = {'query': query, 'page': page, 'sort': sort},
            newLink = $(this).prop('href');

        if ($(this).hasClass('active')) {
            return;
        }

        mainContent.append('<span class="main-content__loading fa fa-spinner fa-spin fa-3x fa-fw"></span>');
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
                $('select').selectric('init');
            }
        });
    });

    mainContent.on('change', '.selectric-dec', function () {
        var query = $('#search-query').val(),
            page = $('#page').val(),
            sort = $('.selectric-dec option:selected').val(),
            sendData = {'query': query, 'page': page, 'sort': sort};

        mainContent.append('<span class="main-content__loading fa fa-spinner fa-spin fa-3x fa-fw"></span>');
        mainContent.css('opacity', 0.5);

        $.get(window.location.href, sendData, function (response) {
            if (response.status == true) {
                mainContent.html(response.page);
                $('html, body').animate({
                    scrollTop: $('.main-content').offset().top
                }, 400);
                mainContent.css('opacity', 1);
                $('.main-content__loading').remove();
                $('select').selectric('init');
            }
        });
    })
});