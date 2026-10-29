<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Admin login') }} | {{ __('Manda Blood Donation Program') }}</title>
    <link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/login.css') }}">
    @include('partials.translations')
</head>

<body>
    @include('partials.preloader')
    <header class="login-header">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true"><i
                    class="fi fi-rr-heart"
                    aria-hidden="true"></i></span><span><strong>{{ __('Manda Blood Donation Program') }}</strong><small>DONATE
                    BLOOD, SAVE LIVES</small></span></a>
        @include('partials.language-switcher')
    </header>
    <main class="login-shell">
        <section class="story" aria-label="{{ __('Manda Blood Donation Program') }}">
            <div class="story-copy">
                <div class="eyebrow"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Together for humanity') }}
                </div>
                <h1>{{ __('One bag of blood,') }}<br><span>{{ __('A new life.') }}</span></h1>
                <p>{{ __('Connect blood donors with those in need. Your small contribution can save a life.') }}</p>
                <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-users"></i></span>
                    <div>
                        <strong>{{ __('Support the community') }}</strong><small>{{ __('Donors, requests and donations — in one place.') }}</small>
                    </div>
                </div>
                <div class="mission"><span class="mission-icon" aria-hidden="true"><i
                            class="fi fi-rr-shield-check"></i></span>
                    <div>
                        <strong>{{ __('Safe & reliable') }}</strong><small>{{ __('Verified information and secure access.') }}</small>
                    </div>
                </div>
                <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-heart"></i></span>
                    <div>
                        <strong>{{ __('Save lives together') }}</strong><small>{{ __('A healthier and stronger tomorrow.') }}</small>
                    </div>
                </div>
            </div>
            <div class="donation-art" aria-hidden="true">
                <svg class="blood-illustration" viewBox="0 0 480 440" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="drop" x1="170" y1="110" x2="320" y2="340"
                            gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ff8197" />
                            <stop offset=".38" stop-color="#f6093d" />
                            <stop offset=".78" stop-color="#c9002b" />
                            <stop offset="1" stop-color="#ff365e" />
                        </linearGradient>
                        <linearGradient id="palm" x1="130" y1="290" x2="330" y2="420"
                            gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ffe5d9" />
                            <stop offset="1" stop-color="#d99981" />
                        </linearGradient>
                        <filter id="shadow" x="-50%" y="-30%" width="200%" height="180%">
                            <feDropShadow dx="0" dy="18" stdDeviation="14" flood-color="#c40835"
                                flood-opacity=".19" />
                        </filter>
                    </defs>
                    <ellipse cx="240" cy="383" rx="129" ry="19" fill="#df9da8"
                        opacity=".15" />
                    <path
                        d="M65 304C85 280 107 280 131 297L201 333C216 341 229 357 240 373C206 405 168 412 132 391L65 347Z"
                        fill="url(#palm)" />
                    <path
                        d="M415 304C395 280 373 280 349 297L279 333C264 341 251 357 240 373C274 405 312 412 348 391L415 347Z"
                        fill="url(#palm)" />
                    <path
                        d="M100 289C107 274 121 277 135 289L195 327M121 276C130 266 142 273 154 286L205 324M380 289C373 274 359 277 345 289L285 327M359 276C350 266 338 273 326 286L275 324"
                        stroke="#f0c0ab" stroke-width="17" stroke-linecap="round" />
                    <path
                        d="M240 65C223 124 154 204 154 257C154 308 192 341 240 341C288 341 326 308 326 257C326 204 257 124 240 65Z"
                        fill="url(#drop)" filter="url(#shadow)" />
                    <path d="M231 105C219 158 173 219 171 252" stroke="white" stroke-opacity=".6" stroke-width="8"
                        stroke-linecap="round" />
                    <path d="M240 303L205 271C173 238 220 210 240 241C260 210 307 238 275 271L240 303Z"
                        fill="white" />
                    <path d="M110 196C125 119 165 96 207 112M335 230C386 226 419 175 410 134" stroke="#ff9bb0"
                        stroke-width="2" stroke-linecap="round" stroke-dasharray="3 8" />
                    <circle cx="183" cy="112" r="4" fill="#ffb0c1" />
                </svg>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="login-card">
                <h2 id="login-title">{{ __('Welcome back!') }}</h2>
                <p class="intro">{{ __('Log in to your account to access the dashboard.') }}</p>
                @if (session('status'))
                    <div class="notice success" role="status"><i class="fi fi-rr-check-circle"
                            aria-hidden="true"></i> {{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="notice" id="login-errors" role="alert">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="field">
                        <label for="email">{{ __('Email address') }}</label>
                        <div class="input-wrap"><i class="fi fi-rr-envelope" aria-hidden="true"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="{{ __('Enter your email address') }}" autocomplete="username" required
                                autofocus @error('email') aria-invalid="true" aria-describedby="login-errors" @enderror>
                        </div>
                    </div>
                    <div class="field">
                        <label for="password">{{ __('Password') }}</label>
                        <div class="input-wrap"><i class="fi fi-rr-lock" aria-hidden="true"></i>
                            <input id="password" type="password" name="password"
                                placeholder="{{ __('Enter your password') }}" autocomplete="current-password"
                                required
                                @error('password') aria-invalid="true" aria-describedby="login-errors" @enderror>
                            <button class="password-toggle" id="password-toggle" type="button"
                                aria-controls="password" aria-pressed="false" aria-label="{{ __('Show') }}"
                                hidden><i class="fi fi-rr-eye-crossed" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <details class="form-help">
                        <summary>{{ __('Forgot password?') }}</summary>
                        <p>{{ __('Contact the system administrator if you have trouble accessing your account.') }}</p>
                    </details>
                    <button type="submit" class="submit">{{ __('Log in') }} <span
                            aria-hidden="true">→</span></button>
                </form>
                <div class="access-note">{{ __('OR') }}</div>
                <div class="admin-badge"><span class="badge"><i class="fi fi-rr-lock" aria-hidden="true"></i>
                        {{ __('Admin panel') }}</span></div>
            </div>
        </section>
    </main>
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
