$(document).ready(function(){
    "use strict";
	/*
	 ==============================================================
	 Accordion Script Start
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