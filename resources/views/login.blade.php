<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Admin login') }} | {{ __('Manda Blood Donation Program') }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f6f8fa;color:#182331;font-family:'Hind Siliguri',sans-serif}a{color:inherit;text-decoration:none}button,input{font:inherit}button{cursor:pointer}.login-shell{min-height:100svh;display:grid;grid-template-columns:1fr 1fr}.story{background:#fff0f1;padding:48px clamp(30px,6vw,100px);display:flex;flex-direction:column;position:relative;overflow:hidden}.brand{display:flex;align-items:center;gap:12px;position:relative;z-index:1}.brand-mark{width:46px;height:46px;border-radius:14px;background:#e33c49;display:grid;place-items:center;color:white;font-size:29px}.brand strong{font-size:19px;display:block}.brand small{font-size:10px;letter-spacing:2px;color:#8d6268}.story-copy{margin:auto 0;padding:60px 0;position:relative;z-index:1}.eyebrow{font-size:12px;color:#c72d3a;font-weight:600;letter-spacing:1px}.story h1{font-size:clamp(34px,3.8vw,56px);line-height:1.4;margin:18px 0;color:#31212a;letter-spacing:-1px}.story h1 span{color:#df3947}.story p{color:#8b6570;font-size:16px;line-height:1.9;max-width:390px}.mission{display:flex;align-items:center;gap:14px;margin-top:32px;padding-top:26px;border-top:1px solid #eacfd3}.mission-icon{background:white;color:#df3947;border-radius:12px;padding:12px;font-size:23px}.mission strong{display:block;font-size:15px}.mission small{color:#946e78}.story-footer{font-size:12px;color:#946e78}.form-side{padding:40px 28px;display:flex;align-items:center;justify-content:center}.login-card{width:100%;max-width:410px}.back{font-size:13px;color:#7a8591;display:inline-block;margin-bottom:45px}.badge{display:inline-flex;align-items:center;gap:7px;border:1px solid #f3d9dd;background:#fff3f4;color:#d33643;padding:7px 12px;border-radius:30px;font-size:12px}.badge svg{width:14px;height:14px}.login-card h2{font-size:32px;margin:20px 0 6px}.intro{font-size:14px;color:#84909b;margin:0 0 30px}.field{margin-bottom:20px}.field label{display:block;font-size:14px;font-weight:600;margin-bottom:8px}.field input{width:100%;height:50px;border:1px solid #e0e5ea;border-radius:10px;background:white;padding:0 15px;color:#182331;outline:none}.field input:focus{border-color:#e33c49;box-shadow:0 0 0 3px #e33c4914}.field input[aria-invalid=true]{border-color:#e33c49}.password-wrap{position:relative}.password-wrap input{padding-right:70px}.password-toggle{position:absolute;right:8px;top:8px;height:34px;border:0;border-radius:6px;background:#f6f8fa;color:#6c7784;font-size:12px;padding:0 10px}.submit{width:100%;border:0;border-radius:10px;background:#e33c49;color:white;padding:14px;font-weight:600;box-shadow:0 6px 15px #e33c4920}.submit:hover{background:#ca2d3b}.submit span{float:right}.notice{padding:12px 15px;border-radius:10px;margin-bottom:22px;font-size:13px;line-height:1.7;background:#fff0f1;color:#b62836;border:1px solid #f4d1d5}.notice.success{background:#edf9f3;color:#237d5b;border-color:#ccebdc}.access-note{margin-top:25px;border-top:1px solid #e9edf0;padding-top:22px;text-align:center;color:#8b96a0;font-size:12px;line-height:1.9}.access-note strong{display:block;font-weight:500;color:#687581}button:focus-visible,a:focus-visible{outline:3px solid #e33c49;outline-offset:4px}@media(max-width:800px){.login-shell{grid-template-columns:1fr}.story{padding:25px}.story-copy,.story-footer{display:none}.form-side{padding:28px 24px 45px}.back{margin-bottom:28px}.login-card h2{font-size:29px}}
    </style>
@include('partials.translations')
</head>
<body>
<div class="flex justify-end bg-white px-5 py-2">
    @include('partials.language-switcher')
</div>
<main class="login-shell">
    <section class="story" aria-label="{{ __('Manda Blood Donation Program') }}">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true"><i class="fi fi-rr-heart" aria-hidden="true"></i></span><span><strong>{{ __('Manda Blood Donation Program') }}</strong><small>DONATE BLOOD, SAVE LIVES</small></span></a>
        <div class="story-copy">
            <div class="eyebrow">{{ __('Together for humanity') }}</div>
            <h1>{{ __('One bag of blood,') }}<br><span>{{ __('A new life.') }}</span></h1>
            <p>{{ __('Connect blood donors with those in need. Your small contribution can save a life.') }}</p>
            <div class="mission"><span class="mission-icon" aria-hidden="true"><i class="fi fi-rr-heart" aria-hidden="true"></i></span><div><strong>{{ __('Welcome to our lifesaving mission') }}</strong><small>{{ __('Donors, requests and donations — in one place.') }}</small></div></div>
        </div>
        <div class="story-footer">{{ __('Manda, Naogaon · Supporting people, supporting life') }}</div>
    </section>
    <section class="form-side" aria-labelledby="login-title">
        <div class="login-card">
            <a class="back" href="{{ route('home') }}">← {{ __('Back to home') }}</a><br>
            <div class="badge"><i class="fi fi-rr-lock" aria-hidden="true"></i> {{ __('Admin panel') }}</div>
            <h2 id="login-title">{{ __('Welcome back!') }}</h2>
            <p class="intro">{{ __('Log in to your account to access the dashboard.') }}</p>
            @if (session('status'))
                <div class="notice success" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="notice" id="login-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" autocomplete="username" required autofocus @error('email') aria-invalid="true" aria-describedby="login-errors" @enderror>
                </div>
                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="password-wrap">
                        <input id="password" type="password" name="password" placeholder="{{ __('Enter your password') }}" autocomplete="current-password" required @error('password') aria-invalid="true" aria-describedby="login-errors" @enderror>
                        <button class="password-toggle" id="password-toggle" type="button" aria-controls="password" aria-pressed="false" hidden>{{ __('Show') }}</button>
                    </div>
                </div>
                <button type="submit" class="submit">{{ __('Log in') }} <span aria-hidden="true">→</span></button>
            </form>
            <div class="access-note"><strong>{{ __('For authorized administrators only') }}</strong>{{ __('Contact the system administrator if you have trouble accessing your account.') }}</div>
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
        passwordToggle.textContent = isVisible ? t("Hide") : t("Show");
    });
</script>
</body>
</html>
