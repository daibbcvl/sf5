$(document).ready(function () {
    $(".regular").slick({
        dots: false,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 3,
        nextArrow: '<button class="product-widget-arrows product-widget-arrow-right ng-star-inserted" style="top: 50%;"><i class="material-icons">chevron_right</i></button>',
        prevArrow: '<button class="product-widget-arrows product-widget-arrow-left ng-star-inserted" style="top: 50%;"><i class="material-icons">chevron_left</i></button>'
    });

    $(".slide-three").slick({
        dots: false,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        nextArrow: '<button class="product-widget-arrows product-widget-arrow-right ng-star-inserted" style="top: 50%;"><i class="material-icons">chevron_right</i></button>',
        prevArrow: '<button class="product-widget-arrows product-widget-arrow-left ng-star-inserted" style="top: 50%;"><i class="material-icons">chevron_left</i></button>'
    });


});

