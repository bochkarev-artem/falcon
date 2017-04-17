$(document).ready(function(){
    "use strict";

    var isUserLoggedIn = typeof bookRatingPath !== 'undefined';

    $('.book-rating').starRating({
        initialRating: bookRating,
        readOnly: !isUserLoggedIn,
        emptyColor: 'lightgray',
        strokeColor: '#894A00',
        hoverColor: '#228B22',
        activeColor: '#ff4c00',
        strokeWidth: 10,
        starSize: 25,
        useFullStars: false,
        useGradient: false,
        disableAfterRate: false,
        callback: function(currentRating, $el) {
            if (bookRatingPath) {
                $(".book-user-rating").text(userRatingText + ': ' + currentRating);
                $.ajax({
                    method: "POST",
                    url: bookRatingPath,
                    data: {rating: currentRating, book_id: bookId}
                }).done(function(data) {
                    $(".book-rating").starRating('setRating', data.rating);
                    $(".rating-total-value").text(data.total);
                    $(".rating-total-text").text(' ' + votesTotalText);
                    $(".rating-value").text(data.rating);
                });
            }
        }
    });

    var counter = $('.review-counter'),
        counterValue = counter.text();

    $('.review-text').on('keyup', function (e) {
        var length = $(this).val().length,
            remainLength = counterValue - length,
            button = $('.send-review-btn');

        if (remainLength > 0) {
            counter.text(remainLength);
            counter.show();
            if (!button.hasClass('disabled')) {
                button.addClass('disabled');
                button.attr("disabled", true);
            }
        } else {
            counter.hide();
            if (button.hasClass('disabled')) {
                button.removeClass('disabled');
                button.removeAttr('disabled');
            }
        }
    });

    if (bookReviewPath) {
        $('.send-review-btn').on('click', function (e) {
            e.preventDefault();
            var text = $('.review-text').val();

            $.ajax({
                method: "POST",
                url: bookReviewPath,
                data: {review: text, book_id: bookId}
            }).done(function(data) {
                if (data.status) {
                    $('.review-flush-msg').text(bookReviewMsg);
                    $('#review-form').remove();
                }
            });
        });
    }
});