$(document).ready(function(){
    "use strict";

    $('.book-rating').starRating({
        readOnly: true,
        emptyColor: 'lightgray',
        strokeColor: '#894A00',
        hoverColor: '#228B22',
        activeColor: '#ff4c00',
        strokeWidth: 10,
        starSize: 25,
        useFullStars: false,
        useGradient: false
    });
});
