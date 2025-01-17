(function ($) {
    "use strict";

    // Dropdown on mouse hover
    $(document).ready(function () {
        function toggleNavbarMethod() {
            if ($(window).width() > 992) {
                $('.navbar .dropdown').on('mouseover', function () {
                    $('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    $('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                $('.navbar .dropdown').off('mouseover').off('mouseout');
            }
        }
        toggleNavbarMethod();
        $(window).resize(toggleNavbarMethod);
    });
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });
    // Vendor carousel
    $('.vendor-carousel').owlCarousel({
        loop: true,
        margin: 29,
        nav: false,
        autoplay: true,
        smartSpeed: 1000,
        responsive: {
            0:{
                items:2
            },
            576:{
                items:3
            },
            768:{
                items:4
            },
            992:{
                items:5
            },
            1200:{
                items:6
            }
        }
    });
    // Related carousel
    $('.related-carousel').owlCarousel({
        loop: true,
        margin: 29,
        nav: false,
        autoplay: true,
        smartSpeed: 1000,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:2
            },
            768:{
                items:3
            },
            992:{
                items:4
            }
        }
    });
    // Product Quantity
    $('.quantity button').on('click', function () {
        var button = $(this);
        var oldValue = button.parent().parent().find('input').val();
        if (button.hasClass('btn-plus')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        button.parent().parent().find('input').val(newVal);
    });
    // handle the product search filtering
    $('form#product-search').on("submit", function(event){
        event.preventDefault();
        let form = $(event.target);
        let formData = new FormData(document.getElementById('product-search'));
        console.log(document.getElementById('product-search'));
        console.log('formData', formData);
        let location = $(form).attr("action");
        let fdata    = $(form).serialize();
        console.log('fdata', fdata);
        let fmethod  = $(form).attr("method");
        let request  = $.ajax({
            url: location,
            method: fmethod,
            data: fdata
        });
        request.done(function(response, textStatus, jqXHR) {
            $('div#product-workspace').html(response);
        });
        request.fail(function() {
            alert('fail');
        });
    });
    // pagination control
    $(document).on('click', 'ul#pagination-control > li.page-item > a.page-link', function(event) {
        event.preventDefault();
        console.log('making request');
        let href = $(this).attr("href");
        let request = $.ajax({
            url:href,
            dataType:"html",
        });
        request.done(function(response, textStatus, jqXHR){
            console.log('updating html');
            $('div#product-workspace').html(response);
        });
    });
})(jQuery);
