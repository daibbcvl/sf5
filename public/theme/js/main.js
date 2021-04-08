$(document).ready(function() {
    $(".product-tile-button").click(function () {
        window.location.href = $(this).attr('data-url');
    })

});

