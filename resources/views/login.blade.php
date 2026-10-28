<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Admin login') }} | {{ __('Manda Blood Donation Program') }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: #111d2e; background: #fffdfd; font-family: 'Hind Siliguri', sans-serif; }
        body::before { content: ''; position: fixed; inset: 0; z-index: -1; background: radial-gradient(ellipse at 13% 18%, #fff2f3 0, transparent 48%), radial-gradient(ellipse at 28% 100%, #ffe9ec 0, transparent 53%); }
        a { color: inherit; text-decoration: none; }
        button, input { font: inherit; }
        button { cursor: pointer; }
        .login-header { max-width: 1560px; margin: auto; padding: 25px 0; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: flex; align-items: center; gap: 15px; }
        .brand-mark { display: grid; place-items: center; width: 54px; height: 54px; flex-shrink: 0; border-radius: 15px; color: white; background: linear-gradient(135deg, #fb354b, #dc1632); box-shadow: inset 0 0 0 1px #ffffff50; font-size: 32px; }
        .brand strong { display: block; font-size: 23px; line-height: 1.4; }
        .brand small { display: block; margin-top: 7px; color: #8c909e; letter-spacing: 2px; font-size: 12px; font-weight: 600; }
        .login-shell { max-width: 1560px; min-height: calc(100svh - 110px); margin: auto; display: grid; grid-template-columns: 1.4fr 1fr; gap: 64px; align-items: center; padding: 18px 0 50px; }
        .story { position: relative; min-width: 0; padding: 40px 0; }
        .story::before { content: ''; position: absolute; width: 640px; height: 640px; border-radius: 45% 55% 60% 40%; background: #ffe8ec66; top: 170px; left: 260px; transform: rotate(-30deg); z-index: -1; }
        .story-copy { position: relative; z-index: 1; }
        .eyebrow { display: inline-flex; align-items: center; gap: 10px; color: #ef203b; border-radius: 30px; padding: 8px 17px; background: #fce6e9; font-size: 15px; font-weight: 500; }
        .story h1 { margin: 17px 0 18px; font-size: clamp(38px, 3.7vw, 59px); line-height: 1.2; font-weight: 700; letter-spacing: -1.8px; }
        .story h1 span { color: #eb2940; }
        .story p { max-width: 390px; color: #737b8b; font-size: 19px; line-height: 1.65; margin-bottom: 42px; }
        .mission { display: flex; align-items: center; gap: 20px; margin-top: 16px; }
        .mission-icon { width: 62px; height: 62px; display: grid; place-items: center; flex-shrink: 0; border-radius: 50%; background: #ffffffb3; color: #f32442; font-size: 27px; }
        .mission strong { display: block; font-size: 16px; font-weight: 500; }
        .mission small { display: block; margin-top: 4px; color: #7c8494; font-size: 14px; }
        .donation-art { position: absolute; width: 49%; right: -18px; top: 105px; pointer-events: none; }
        .donation-art svg { width: 100%; height: auto; filter: drop-shadow(0 20px 24px #f85a6910); }
        .form-side { position: relative; z-index: 2; width: 100%; }
        .login-card { width: 100%; padding: 43px 58px; border: 1px solid white; border-radius: 24px; background: #ffffffed; box-shadow: 0 15px 65px #36232d12; }
        .login-card h2 { margin: 0 0 4px; font-size: 32px; font-weight: 700; letter-spacing: -.7px; }
        .intro { margin: 0 0 27px; color: #8993a5; font-size: 16px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 7px; font-size: 16px; font-weight: 600; }
        .input-wrap { position: relative; }
        .input-wrap > i { position: absolute; top: 18px; left: 18px; color: #566173; font-size: 19px; pointer-events: none; }
        .field input { width: 100%; height: 55px; padding: 0 48px 0 52px; border: 1px solid #e5e9f0; border-radius: 11px; outline: none; color: #182331; background: #f7f9fc; transition: border-color .2s, box-shadow .2s; }
        .field input::placeholder { color: #8e99ac; }
        .field input:focus { background: white; border-color: #f27387; box-shadow: 0 0 0 3px #fbdce2; }
        .field input[aria-invalid=true] { border-color: #e33c49; }
        .password-toggle { position: absolute; right: 8px; top: 8px; width: 39px; height: 39px; border: 0; border-radius: 8px; color: #626d7f; background: #eef1f7; }
        .password-toggle[hidden] { display: none; }
        .form-help { text-align: right; color: #f0203b; font-size: 14px; margin: 2px 0 25px; }
        .form-help summary { cursor: pointer; list-style: none; }
        .form-help p { margin: 10px 0 0; color: #7c8494; font-size: 13px; }
        .submit { display: flex; align-items: center; justify-content: center; gap: 15px; width: 100%; min-height: 58px; border: 1px solid #dc1632; border-radius: 12px; color: white; background: linear-gradient(#fb354b, #d51e31); box-shadow: 0 7px 15px #b81c3020; font-size: 19px; font-weight: 600; }
        .submit:hover { background: linear-gradient(#ee2840, #c5192d); }
        .notice { padding: 16px; border-radius: 10px; margin-bottom: 26px; font-size: 14px; line-height: 1.7; background: #fff0f1; color: #b62836; border: 1px solid #f4d1d5; }
        .notice.success { display: flex; align-items: center; gap: 12px; background: #edf9f3; color: #19985e; border-color: #ccebdc; }
        .notice.success i { font-size: 22px; }
        .access-note { display: flex; align-items: center; gap: 15px; margin: 25px 0 22px; color: #9aa3b3; font-size: 14px; }
        .access-note::before, .access-note::after { content: ''; height: 1px; background: #edf0f4; flex: 1; }
        .admin-badge { display: flex; justify-content: center; }
        .badge { display: inline-flex; align-items: center; justify-content: center; gap: 12px; padding: 10px 35px; color: #f12642; background: #fff4f5; border: 1px solid #ffe5e9; border-radius: 30px; font-size: 16px; }
        .icon-credit { display: block; text-align: center; color: #9aa3b3; font-size: 10px; padding: 8px; }
        button:focus-visible, a:focus-visible, summary:focus-visible { outline: 3px solid #f27387; outline-offset: 4px; }
        @media (max-width: 1690px) { .login-header, .login-shell { margin-left: 6.8%; margin-right: 6.8%; } .login-shell { gap: 48px; grid-template-columns: 1.45fr 1fr; } .login-card { padding: 40px; } }
        @media (max-width: 1200px) { .login-shell { gap: 30px; grid-template-columns: 1.15fr 1fr; } .donation-art { opacity: .65; width: 43%; top: 175px; right: -10px; } .story h1 { font-size: 43px; } .story p { max-width: 320px; } .login-card { padding: 32px; } .mission { gap: 12px; } .mission small { max-width: 280px; } }
        @media (max-width: 900px) { .login-header { margin: 0; padding: 22px 24px; } .brand strong { font-size: 18px; } .brand small { font-size: 9px; } .login-shell { grid-template-columns: 1fr; margin: 0 auto; max-width: 600px; padding: 12px 24px 36px; gap: 24px; min-height: auto; } .story { padding: 15px 0; } .story h1 { font-size: 40px; } .story p { font-size: 16px; margin-bottom: 0; max-width: 330px; } .mission { display: none; } .donation-art { width: 180px; top: 20px; right: -10px; opacity: .3; } .story::before { display: none; } .login-card { padding: 30px; } }
        @media (max-width: 480px) { .login-header { padding: 18px; gap: 10px; align-items: flex-start; } .brand { gap: 9px; } .brand-mark { width: 40px; height: 40px; border-radius: 11px; font-size: 25px; } .brand strong { max-width: 170px; font-size: 15px; } .brand small { font-size: 7px; letter-spacing: 1px; } .language-switcher-trigger { padding: 9px; gap: 5px; font-size: 12px; } .login-shell { padding: 8px 18px 24px; } .story h1 { font-size: 36px; } .eyebrow { font-size: 12px; } .login-card { padding: 26px 22px; border-radius: 20px; } .login-card h2 { font-size: 28px; } .intro { font-size: 14px; } }
    </style>
@include('partials.translations')
</head>
<body>
@include('partials.preloader')
<header class="login-header">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true"><i class="fi fi-rr-heart" aria-hidden="true"></i></span><span><strong>{{ __('Manda Blood Donation Program') }}</strong><small>DONATE BLOOD, SAVE LIVES</small></span></a>
    @include('partials.language-switcher')
</header>
<main class="login-shell">
    <section class="story" aria-label="{{ __('Manda Blood Donation Program') }}">
        <div class="story-copy">
            <div class="eyebrow"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Together for humanity') }}</div>
            <h1>{{ __('One bag of blood,') }}<br><span>{{ __('A new life.') }}</span></h1>
            <p>{{ __('Connect blood donors with those in need. Your small contribution can save a life.') }}</p>
            <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-users"></i></span><div><strong>{{ __('Support the community') }}</strong><small>{{ __('Donors, requests and donations — in one place.') }}</small></div></div>
            <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-shield-check"></i></span><div><strong>{{ __('Safe & reliable') }}</strong><small>{{ __('Verified information and secure access.') }}</small></div></div>
            <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-heart"></i></span><div><strong>{{ __('Save lives together') }}</strong><small>{{ __('A healthier and stronger tomorrow.') }}</small></div></div>
        </div>
        <div class="donation-art" aria-hidden="true">
                    <svg class="blood-illustration" viewBox="0 0 480 440" fill="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="drop" x1="170" y1="110" x2="320" y2="340" gradientUnits="userSpaceOnUse"><stop stop-color="#ff8197"/><stop offset=".38" stop-color="#f6093d"/><stop offset=".78" stop-color="#c9002b"/><stop offset="1" stop-color="#ff365e"/></linearGradient>
                            <linearGradient id="palm" x1="130" y1="290" x2="330" y2="420" gradientUnits="userSpaceOnUse"><stop stop-color="#ffe5d9"/><stop offset="1" stop-color="#d99981"/></linearGradient>
                            <filter id="shadow" x="-50%" y="-30%" width="200%" height="180%"><feDropShadow dx="0" dy="18" stdDeviation="14" flood-color="#c40835" flood-opacity=".19"/></filter>
                        </defs>
                        <ellipse cx="240" cy="383" rx="129" ry="19" fill="#df9da8" opacity=".15"/>
                        <path d="M65 304C85 280 107 280 131 297L201 333C216 341 229 357 240 373C206 405 168 412 132 391L65 347Z" fill="url(#palm)"/>
                        <path d="M415 304C395 280 373 280 349 297L279 333C264 341 251 357 240 373C274 405 312 412 348 391L415 347Z" fill="url(#palm)"/>
                        <path d="M100 289C107 274 121 277 135 289L195 327M121 276C130 266 142 273 154 286L205 324M380 289C373 274 359 277 345 289L285 327M359 276C350 266 338 273 326 286L275 324" stroke="#f0c0ab" stroke-width="17" stroke-linecap="round"/>
                        <path d="M240 65C223 124 154 204 154 257C154 308 192 341 240 341C288 341 326 308 326 257C326 204 257 124 240 65Z" fill="url(#drop)" filter="url(#shadow)"/>
                        <path d="M231 105C219 158 173 219 171 252" stroke="white" stroke-opacity=".6" stroke-width="8" stroke-linecap="round"/>
                        <path d="M240 303L205 271C173 238 220 210 240 241C260 210 307 238 275 271L240 303Z" fill="white"/>
                        <path d="M110 196C125 119 165 96 207 112M335 230C386 226 419 175 410 134" stroke="#ff9bb0" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 8"/>
                        <circle cx="183" cy="112" r="4" fill="#ffb0c1"/>
                    </svg>
        </div>
    </section>
    <section class="form-side" aria-labelledby="login-title">
        <div class="login-card">
            <h2 id="login-title">{{ __('Welcome back!') }}</h2>
            <p class="intro">{{ __('Log in to your account to access the dashboard.') }}</p>
            @if (session('status'))
                <div class="notice success" role="status"><i class="fi fi-rr-check-circle" aria-hidden="true"></i> {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="notice" id="login-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="email">{{ __('Email address') }}</label>
                    <div class="input-wrap"><i class="fi fi-rr-envelope" aria-hidden="true"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email address') }}" autocomplete="username" required autofocus @error('email') aria-invalid="true" aria-describedby="login-errors" @enderror>
                    </div>
                </div>
                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrap"><i class="fi fi-rr-lock" aria-hidden="true"></i>
                        <input id="password" type="password" name="password" placeholder="{{ __('Enter your password') }}" autocomplete="current-password" required @error('password') aria-invalid="true" aria-describedby="login-errors" @enderror>
                        <button class="password-toggle" id="password-toggle" type="button" aria-controls="password" aria-pressed="false" aria-label="{{ __('Show') }}" hidden><i class="fi fi-rr-eye-crossed" aria-hidden="true"></i></button>
                    </div>
                </div>
                <details class="form-help"><summary>{{ __('Forgot password?') }}</summary><p>{{ __('Contact the system administrator if you have trouble accessing your account.') }}</p></details>
                <button type="submit" class="submit">{{ __('Log in') }} <span aria-hidden="true">→</span></button>
            </form>
            <div class="access-note">{{ __('OR') }}</div>
            <div class="admin-badge"><span class="badge"><i class="fi fi-rr-lock" aria-hidden="true"></i> {{ __('Admin panel') }}</span></div>
        </div>
    </section>
</main>
<div style="text-align:center;padding:12px"><a href="https://www.flaticon.com/uicons" class="icon-credit" target="_blank" rel="noopener noreferrer">UIcons by Flaticon</a></div>
<script>
    const t = key => window.donationTranslations[key] ?? key;
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('password');
    passwordToggle.hidden = false;
    passwordToggle.addEventListener('click', () => {
        const isVisible = passwordInput.type === 'password';
        passwordInput.type = isVisible ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(isVisible));
        passwordToggle.setAttribute('aria-label', isVisible ? t("Hide") : t("Show"));
        passwordToggle.querySelector('i').className = isVisible ? 'fi fi-rr-eye' : 'fi fi-rr-eye-crossed';
    });
</script>
</body>
</html>
