$(document).ready(function(){
    "use strict";

    var isUserLoggedIn = typeof bookRatingPath !== 'undefined';

    $(".book-rating").starRating({
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
});