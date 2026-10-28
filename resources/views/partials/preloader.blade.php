<style>
    .page-preloader {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: grid;
        place-items: center;
        background: #fffafb;
        color: #ed073a;
        opacity: 1;
        transition: opacity 240ms ease;
    }
    .page-preloader[hidden] { display: none; }
    .page-preloader.is-leaving { opacity: 0; pointer-events: none; }
    .page-preloader__heart {
        width: 80px;
        height: 80px;
        overflow: visible;
        filter: drop-shadow(0 8px 18px #ed073a26);
        animation: preloader-heartbeat 1.2s ease-in-out infinite;
    }
    @keyframes preloader-heartbeat {
        0%, 40%, 100% { transform: scale(1); }
        12%, 28% { transform: scale(1.16); }
        20% { transform: scale(1.04); }
    }
    @media (prefers-reduced-motion: reduce) {
        .page-preloader { transition: none; }
        .page-preloader__heart { animation: none; }
    }
</style>
<section id="page-preloader" class="page-preloader" role="status" aria-label="{{ __('Manda Blood Donation Program') }}" hidden>
    <svg class="page-preloader__heart" viewBox="0 0 64 64" fill="none" aria-hidden="true">
        <path d="M32 55 9 33C-8 16 17-2 32 15 47-2 72 16 55 33Z" fill="currentColor"/>
        <path d="M10 32h12l5-10 7 21 5-11h15" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</section>
<script>
    (() => {
        const preloader = document.getElementById('page-preloader');
        let dismissed = false;
        const dismissPreloader = () => {
            if (dismissed) return;
            dismissed = true;
            window.clearTimeout(fallbackTimeout);
            preloader.classList.add('is-leaving');
            window.setTimeout(() => preloader.remove(), 250);
        };
        const fallbackTimeout = window.setTimeout(dismissPreloader, 8000);

        preloader.hidden = false;
        if (document.readyState === 'complete') {
            dismissPreloader();
        } else {
            window.addEventListener('load', dismissPreloader, { once: true });
        }
        window.addEventListener('pagehide', dismissPreloader, { once: true });
        window.addEventListener('pageshow', event => {
            if (event.persisted) preloader.remove();
        });
    })();
</script>
