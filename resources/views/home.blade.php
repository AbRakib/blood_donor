<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>{{ __('Manda Blood Donation Program') }}</title>
        <link rel="stylesheet" href="{{ asset('frontend/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/fonts.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    @include('partials.translations')
</head>
    <body data-donation-page="home">
        <div class="bg-[#9f1930] text-white text-center text-sm py-1">
            <i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Donate blood, save lives — stand with humanity') }}
        </div>
        @include('partials.preloader')
        <header class="sticky top-0 bg-white/95 border-b z-20">
            <div class="max-w-7xl mx-auto px-5 py-4 flex flex-wrap gap-4 items-center justify-between">
                <a href="#home" class="flex gap-3 items-center">
                    <div class="bg-[#d32340] text-white rounded-xl w-12 h-12 grid place-items-center text-3xl">
                        <i class="fi fi-rr-heart" aria-hidden="true"></i>
                    </div>
                    <div>
                        <strong class="text-xl">{{ $companyName }}</strong>
                        <p class="text-xs text-gray-500">{{ __('A drop of blood, a new life') }}</p>
                    </div>
                </a>
                <nav aria-label="{{ __('Quick links') }}" class="hidden md:flex gap-7 text-sm font-semibold">
                    <a href="#home">{{ __('Home') }}</a>
                    <a href="#donors">{{ __('Find donors') }}</a>
                    <a href="#about">{{ __('About us') }}</a>
                    <a href="#how-it-works">{{ __('How it works') }}</a>
                    <a href="#contact">{{ __('Contact') }}</a>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    @include('partials.language-switcher')
                    <button class="btn" onclick="showModal('register')">
                        {{ __('Become a donor') }}
                    </button>
                </div>
            </div>
        </header>
        <main>
        <section id="home" class="hero">
            <div class="max-w-7xl mx-auto px-5 py-20 grid md:grid-cols-2 gap-12 items-center">
                <div class="reveal">
                    <span class="bg-red-50 red rounded-full px-4 py-2 font-semibold text-sm"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Together in service of humanity') }}</span>
                    <h1 data-donor-heading aria-label="{{ __('Your blood') }} {{ __('Can save') }} {{ __('A precious life') }}" class="text-4xl md:text-6xl font-bold leading-tight mt-7"><span data-typing-part aria-hidden="true">{{ __('Your blood') }} </span><span data-typing-part aria-hidden="true" class="red">{{ __('Can save') }}</span><span data-typing-part aria-hidden="true"> {{ __('A precious life') }}</span></h1>
                    <p class="text-gray-600 text-lg leading-8 my-7">{{ __('Connecting blood donors in Manda and nearby areas. Find a suitable donor when you need one.') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <a class="btn" href="#donors"><i class="fi fi-rr-search" aria-hidden="true"></i> {{ __('Find donors') }} <span aria-hidden="true">→</span></a>
                        <button onclick="showModal('register')" class="border bg-white px-6 rounded-xl font-bold">
                            {{ __('I want to donate blood') }}
                        </button>
                    </div>
                </div>
                <div class="hero-art" role="img" aria-label="{{ __('Donate blood, save lives') }}">
                    <div class="hero-orbit"></div>
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
                    <div class="floating-note note-donate"><span class="note-icon"><i class="fi fi-rr-user" aria-hidden="true"></i></span><div><b>{{ __('Donate blood, save lives') }}</b><small>{{ __('A drop of blood, a new life') }}</small></div></div>
                    <div class="floating-note note-hero"><span class="note-icon"><i class="fi fi-rr-heart" aria-hidden="true"></i></span><div><b>{{ __('Humanitarian support') }}</b><small>{{ __('Support in emergencies') }}</small></div></div>
                    <div class="floating-note note-community"><span class="note-icon"><i class="fi fi-rr-users" aria-hidden="true"></i></span><div><b>{{ __('Together for humanity') }}</b><small>{{ __('Volunteer initiative') }}</small></div></div>
                </div>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-5 -mt-7 relative">
            <div class="card grid grid-cols-2 md:grid-cols-4 text-center gap-5">
                <div>
                    <div class="stat-value">
                        <i class="stat-icon fi fi-rr-users" aria-hidden="true"></i>
                        <b id="donorTotal" class="text-3xl red">—</b>
                    </div>
                    <p class="text-gray-500">{{ __('Available donors') }}</p>
                </div>
                <div>
                    <div class="stat-value">
                        <i class="stat-icon fi fi-rr-blood" aria-hidden="true"></i>
                        <b id="groupTotal" class="text-3xl red">—</b>
                    </div>
                    <p class="text-gray-500">{{ __('Blood group') }}</p>
                </div>
                <div>
                    <div class="stat-value">
                        <i class="stat-icon fi fi-rr-marker" aria-hidden="true"></i>
                        <b class="text-3xl red">{{ __('24/7') }}</b>
                    </div>
                    <p class="text-gray-500">{{ __('Search') }}</p>
                </div>
                <div>
                    <div class="stat-value">
                        <i class="stat-icon fi fi-rr-heart" aria-hidden="true"></i>
                        <b class="text-3xl red">{{ __('100') }}%</b>
                    </div>
                    <p class="text-gray-500">{{ __('Volunteer initiative') }}</p>
                </div>
            </div>
        </section>
        <section id="donors" class="max-w-7xl mx-auto px-5 py-20">
            <div class="text-center mb-9">
                <span class="red font-bold">{{ __('Donor directory') }}</span>
                <h2 data-donor-heading aria-label="{{ __('Find the help') }} {{ __('Find donors') }}" class="text-3xl md:text-4xl font-bold mt-3"><span data-typing-part aria-hidden="true">{{ __('Find the help') }} </span><span data-typing-part aria-hidden="true" class="red">{{ __('Find donors') }}</span></h2>
                <p class="text-gray-500 mt-3">{{ __('Search easily by blood group and area') }}</p>
            </div>
            <div class="card grid md:grid-cols-4 gap-4">
                <label class="search-field"><i class="fi fi-rr-drop" aria-hidden="true"></i><span><span class="field-label">{{ __('Blood group') }}</span><select id="blood" class="field">
                    <option value="">{{ __('All blood groups') }}</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                    <option>O+</option>
                    <option>O-</option>
                </select></span></label>
                <label class="search-field"><i class="fi fi-rr-marker" aria-hidden="true"></i><span><span class="field-label">{{ __('Address / area') }}</span><select id="area" class="field">
                    <option value="">{{ __('All areas') }}</option>
                </select></span></label>
                <label class="search-field"><i class="fi fi-rr-user" aria-hidden="true"></i><span><span class="field-label">{{ __('Full name') }}</span><input id="query" class="field" placeholder="{{ __('Enter donor name') }}"></span></label>
                <button class="btn" id="searchBtn"><i class="fi fi-rr-search" aria-hidden="true"></i>
                    {{ __('Search') }}
                </button>
            </div>
            <div class="flex justify-between mt-10 mb-5">
                <h3 class="font-bold text-xl">{{ __('Donor list') }}</h3>
                <span id="result" class="text-gray-500"></span>
            </div>
            <div id="grid" aria-live="polite" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            </div>
            <p class="text-gray-400 text-xs mt-5">* {{ __('The displayed names and details are fictional. They do not represent real donors.') }}</p>
        </section>
        <section id="about" class="bg-[#fff1f3] py-20">
            <div class="max-w-7xl mx-auto px-5 grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="red font-bold">{{ __('Our mission') }}</span>
                    <h2 data-donor-heading aria-label="{{ __('So a lack of blood') }} {{ __('Let no life stop') }}" class="text-4xl font-bold my-5"><span data-typing-part aria-hidden="true">{{ __('So a lack of blood') }} </span><span data-typing-part aria-hidden="true" class="red">{{ __('Let no life stop') }}</span></h2>
                    <p class="text-gray-600 leading-8">{{ __('Manda Blood Donation Program is a volunteer platform for donor registration, searching by blood group and area, and consent-based contact.') }}</p>
                    <button class="btn mt-6" onclick="showModal('register')">
                        <i class="fi fi-rr-user-add" aria-hidden="true"></i>
                        {{ __('Register') }}
                    </button>
                </div>
                <div id="how-it-works" class="mission-features grid grid-cols-2 gap-4">
                    <div class="card">
                        <div class="text-4xl red">
                            <i class="fi fi-rr-heart" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Humanitarian support') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Support in emergencies') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            <i class="fi fi-rr-search" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Easy search') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('By area and blood group') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            <i class="fi fi-rr-users" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Volunteer initiative') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Together for humanity') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            <i class="fi fi-rr-shield-check" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Privacy') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Consent-based contact') }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-5 py-16">
            <div class="bg-[#a51d35] text-white rounded-3xl p-10 flex flex-col md:flex-row gap-6 items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold">{{ __('You can be the hero in someone\'s life') }}</h2>
                    <p class="mt-3 text-white/80">{{ __('Register as a blood donor and stand with humanity.') }}</p>
                </div>
                <button class="bg-white text-[#a51d35] px-7 py-4 rounded-xl font-bold" onclick="showModal('register')">
                    {{ __('Register now →') }}
                </button>
            </div>
        </section>
        </main>
        <footer id="contact" class="bg-[#192333] text-white">
            <div class="max-w-7xl mx-auto px-5 py-12 grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-xl"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Manda Blood Donation Program') }}</h3>
                    <p class="text-gray-400 mt-3">{{ __('Donate blood, save lives.') }}</p>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('Quick links') }}</h3>
                    <div class="footer-links text-gray-400 mt-3"><a href="#home">{{ __('Home') }}</a><a href="#donors">{{ __('Find donors') }}</a><a href="#about">{{ __('About us') }}</a><a href="#how-it-works">{{ __('How it works') }}</a></div>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('Address') }}</h3>
                    <p class="text-gray-400 mt-3"><i class="fi fi-rr-marker" aria-hidden="true"></i> {{ __('Manda, Naogaon, Bangladesh') }}</p>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('Together for humanity') }}</h3>
                    <button class="btn mt-4" onclick="showModal('register')">{{ __('Become a donor') }} <span aria-hidden="true">→</span></button>
                </div>
            </div>
            <div class="border-t border-white/10 text-center text-gray-400 py-5 text-sm">
                © {{ __('2026 Manda Blood Donation Program') }}
            </div>
        </footer>
        <div id="modal" class="donation-modal hidden fixed inset-0 z-50 p-4 items-center justify-center">
            <div role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc" class="donation-modal-panel">
                <button aria-label="{{ __('Close') }}" onclick="hideModal()" class="donation-modal-close">
                    ×
                </button>
                <h2 id="modalTitle" class="text-2xl font-bold mb-3">{{ __('Donor registration') }}</h2>
                <p id="modalDesc" class="text-gray-500 text-sm mb-5">{{ __('Complete the details below') }}</p>
                <form id="form" class="donation-modal-form">
                    <label class="modal-field"><span>{{ __('Full name') }}</span><input name="name" required class="field" autocomplete="name" placeholder="{{ __('Full name') }}"></label>
                    <label class="modal-field"><span>{{ __('Mobile number') }}</span><input name="phone" type="tel" required class="field" autocomplete="tel" placeholder="{{ __('Mobile number') }}" inputmode="tel"></label>
                    <label class="modal-field" data-registration-field><span>{{ __('Blood group') }}</span><select name="blood_group_id" required class="field">
                        <option value="">{{ __('Blood group') }}</option>
                        <option>A+</option>
                        <option>A-</option>
                        <option>B+</option>
                        <option>B-</option>
                        <option>AB+</option>
                        <option>AB-</option>
                        <option>O+</option>
                        <option>O-</option>
                    </select></label>
                    <label class="modal-field" data-registration-field><span>{{ __('Address / area') }}</span><input name="area" required class="field" autocomplete="street-address" placeholder="{{ __('Address / area') }}"></label>
<label class="modal-field" data-contact-field hidden><span>{{ __('Reason for contact') }}</span><textarea name="message" class="field" placeholder="{{ __('Reason for contact') }}" hidden></textarea></label>
                    <label class="modal-consent">
                        <input name="consent_to_contact" value="1" required type="checkbox">
                        {{ __('I consent to data collection and contact requests.') }}
                    </label>
                    <button class="btn w-full" type="submit">
                        {{ __('Submit') }}
                    </button>
                </form>
                <p id="message" class="hidden text-sm text-green-700 bg-green-50 rounded-lg p-3 mt-4">
                </p>
            </div>
        </div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>window.donationRoutes = {{ Illuminate\Support\Js::from([
'groups' => route('blood-groups.index'), 'locations' => route('locations.index'),
'donors' => route('donors.index'), 'register' => route('donors.store'), 'contact' => route('contact-requests.store'),
'admin' => route('admin.dashboard'), 'adminDonors' => route('admin.blood-donors.index'),
'requests' => route('admin.blood-requests.index'), 'donations' => route('admin.donations.index'),
'contacts' => route('admin.contact-requests.index'), 'settings' => route('admin.settings.index'),
'reports' => route('admin.reports.index'),
'locationStore' => route('admin.locations.store', ['type' => 'districts']), 'login' => route('login'),
]) }};</script>
<link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
        <script type="module" src="{{ asset('frontend/js/main.js') }}"></script>

    </body>
</html>


