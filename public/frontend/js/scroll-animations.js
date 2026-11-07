(function () {
    'use strict';

    const main = document.querySelector('[data-donation-page="home"] main');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!main || !('IntersectionObserver' in window) || !Element.prototype.animate) {
        return;
    }

    const prepared = new WeakSet();
    const animations = new Map();
    const selector = 'section > *, .hero-art, .mission-features > .card, #grid > .card, .top-donors-carousel, section img';
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            }

            const animation = animations.get(entry.target);
            if (animation) {
                animation.play();
            }
            observer.unobserve(entry.target);
        });
    }, {threshold: 0, rootMargin: '0px 0px -32px 0px'});

    function prepareElements() {
        if (reducedMotion.matches) {
            return;
        }

        main.querySelectorAll(selector).forEach(element => {
            if (prepared.has(element) || element.closest('.owl-item')) {
                return;
            }
            prepared.add(element);

            const isArtwork = element.matches('.hero-art, img');
            const siblingIndex = [...element.parentElement.children].indexOf(element);
            const animation = element.animate([
                {opacity: 0, translate: isArtwork ? '0 28px' : '0 36px', scale: isArtwork ? '0.96' : '1'},
                {opacity: 1, translate: '0 0', scale: '1'},
            ], {
                duration: isArtwork ? 850 : 650,
                delay: Math.min(siblingIndex % 4, 3) * 90,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            });
            animation.pause();
            animations.set(element, animation);
            animation.onfinish = () => {
                animation.cancel();
                animations.delete(element);
            };
            observer.observe(element);
        });
    }

    main.addEventListener('focusin', event => {
        animations.forEach((animation, element) => {
            if (element.contains(event.target)) {
                animation.finish();
            }
        });
    });

    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) {
            observer.disconnect();
            animations.forEach(animation => animation.cancel());
            animations.clear();
        } else {
            prepareElements();
        }
    });

    prepareElements();
    const donorGrid = main.querySelector('#grid');
    if (donorGrid) {
        new MutationObserver(prepareElements).observe(donorGrid, {childList: true});
    }
}());
