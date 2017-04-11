$(document).ready(function(){
    "use strict";

    var isUserLoggedIn = typeof bookRatingPath !== 'undefined';

    $(".book-rating").starRating({
        initialRating: bookRating/2,
        readOnly: !isUserLoggedIn,
        strokeColor: '#894A00',
        strokeWidth: 10,
        starSize: 25,
        useFullStars: false,
        disableAfterRate: false,
        callback: function(currentRating, $el) {
            if (bookRatingPath) {
                $(".book-user-rating").text(userRatingText + ' ' + currentRating);
                $.ajax({
                    method: "POST",
                    url: bookRatingPath,
                    data: {rating: currentRating}
                }).done(function(data) {
                    $(".book-rating").starRating('setRating', data.rating);
                });
            }
        }
    });
});