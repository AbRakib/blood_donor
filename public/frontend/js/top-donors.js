(function () {
    'use strict';

    if (!window.jQuery || !window.jQuery.fn.owlCarousel) {
        return;
    }

    window.jQuery(function ($) {
        $('.top-donors-carousel').owlCarousel({
            loop: false,
            margin: 20,
            nav: true,
            dots: true,
            autoplay: false,
            smartSpeed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 350,
            navText: ['<span aria-hidden="true"></span>', '<span aria-hidden="true"></span>'],
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                900: { items: 3 },
                1200: { items: 4 }
            },
            onInitialized: function () {
                var carousel = this.$element;
                var isBengali = document.documentElement.lang === 'bn';
                carousel.find('.owl-prev').attr('aria-label', isBengali ? 'আগের রক্তদাতারা' : 'Previous donors');
                carousel.find('.owl-next').attr('aria-label', isBengali ? 'পরের রক্তদাতারা' : 'Next donors');
                carousel.find('.owl-dot').each(function (index) {
                    $(this).attr('aria-label', (isBengali ? 'পৃষ্ঠা ' : 'Page ') + (index + 1));
                });
            }
        });
    });
})();
