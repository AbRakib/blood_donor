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
        @include('partials.language-switcher')
    </header>
    <svg class="background-waves" viewBox="0 0 1440 400" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 40C270 55 480 390 850 365S1210 205 1440 175V400H0Z" fill="#fce2eb" />
        <path d="M0 170C340 130 530 435 940 390S1270 315 1440 290V400H0Z" fill="#fdebf1" />
    </svg>
    <main class="login-shell">
        <div class="login-panel">
            <a class="illustration-side" href="{{ route('home') }}">
                <svg class="donation-illustration" viewBox="0 0 280 280" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="blood-drop" x1="122" y1="64" x2="166" y2="220" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ff557a" /><stop offset=".45" stop-color="#ff003e" /><stop offset="1" stop-color="#d50035" />
                        </linearGradient>
                        <linearGradient id="hands" x1="140" y1="202" x2="140" y2="270" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ffdfc9" /><stop offset="1" stop-color="#f4b69d" />
                        </linearGradient>
                    </defs>
                    <circle cx="140" cy="158" r="95" fill="white" fill-opacity=".42" />
                    <path d="M42 177C22 147 66 104 88 123S120 174 159 151 218 126 204 87 236 35 246 68" stroke="#ff7493" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 5" />
                    <path d="M140 70C133 101 93 143 93 178a47 47 0 0 0 94 0c0-35-36-73-47-108Z" fill="url(#blood-drop)" />
                    <path d="M139 88c-10 38-38 63-37 91 5-25 37-48 37-91Z" fill="#ff9cb2" fill-opacity=".85" />
                    <path d="M140 169c-20-24-39 1-21 17l21 18 21-18c18-16-1-41-21-17Z" fill="white" />
                    <path d="M52 208c-6-15 4-20 12-10-2-17 10-20 18-7 4-10 14-9 20 0l38 37-18 25-43-15Z" fill="#eeb399" />
                    <path d="M228 208c6-15-4-20-12-10 2-17-10-20-18-7-4-10-14-9-20 0l-38 37 18 25 43-15Z" fill="#eeb399" />
                    <path d="M42 211c-8-9-18-1-10 10 20 29 48 48 71 44 17-3 28-13 37-29-13-17-26-20-40-20l-28-13c-14-5-19 4-9 12l17 12Z" fill="url(#hands)" />
                    <path d="M238 211c8-9 18-1 10 10-20 29-48 48-71 44-17-3-28-13-37-29 13-17 26-20 40-20l28-13c14-5 19 4 9 12l-17 12Z" fill="url(#hands)" />
                </svg>
                <strong>{{ __('Manda Blood Donation Program') }}</strong>
                <span>{{ __('Donate blood, save lives') }}</span>
            </a>
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
                            <div class="input-wrap">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder=" " autocomplete="username" required
                                    autofocus @error('email') aria-invalid="true" aria-describedby="login-errors" @enderror>
                                <label for="email"><i class="fi fi-rr-envelope" aria-hidden="true"></i> {{ __('Email or Phone') }}</label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="input-wrap">
                                <input id="password" type="password" name="password"
                                    placeholder=" " autocomplete="current-password"
                                    required
                                    @error('password') aria-invalid="true" aria-describedby="login-errors" @enderror>
                                <label for="password"><i class="fi fi-rr-lock" aria-hidden="true"></i> {{ __('Password') }}</label>
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
                    <div class="access-note" aria-hidden="true">{{ __('or') }}</div>
                    <div class="admin-badge"><span class="badge"><i class="fi fi-rr-user" aria-hidden="true"></i>
                            {{ __('Admin panel') }}</span></div>
                    <a class="back-home" href="{{ route('home') }}">{{ __('Back to home page') }}</a>
                </div>
            </section>
        </div>
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
