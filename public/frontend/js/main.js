if (document.body.dataset.donationPage === 'home') import('./donation-home.js');

// Native details keeps the language choices available without JavaScript.
document.querySelectorAll('.language-switcher').forEach(switcher => {
    document.addEventListener('click', event => {
        if (!switcher.contains(event.target)) {
            switcher.open = false;
        }
    });
    switcher.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            switcher.open = false;
            switcher.querySelector('summary').focus();
        }
    });
});
