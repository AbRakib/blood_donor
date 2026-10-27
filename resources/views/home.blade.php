<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>{{ __('Manda Blood Donation Program') }}</title>
        @vite('resources/css/app.css')
        <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <style>
            body {
                font-family: 'Hind Siliguri',sans-serif;
                color: #202938;
                background: #fafafa;
            }

            html {
                scroll-behavior: smooth;
            }

            .red {
                color: #d32340;
            }

            .btn {
                background: #d32340;
                color: white;
                padding: 13px 24px;
                border-radius: 12px;
                font-weight: 700;
            }

            .field {
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 13px;
                width: 100%;
                background: white;
            }

            .card {
                background: white;
                border: 1px solid #f0f0f0;
                border-radius: 20px;
                padding: 24px;
                box-shadow: 0 8px 28px #17203609;
                transition: .3s;
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 16px 35px #17203616;
            }

            .hero {
                background: radial-gradient(circle at 85% 40%,#ffd9df,transparent 35%),linear-gradient(120deg,#fff,#fff3f5);
            }
        </style>
    @include('partials.translations')
</head>
    <body data-donation-page="home">
        <div class="bg-[#9f1930] text-white text-center text-sm py-2">
            <i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Donate blood, save lives — stand with humanity') }}
        </div>
        <header class="sticky top-0 bg-white/95 border-b z-20">
            <div class="max-w-7xl mx-auto px-5 py-4 flex flex-wrap gap-4 items-center justify-between">
                <a href="#home" class="flex gap-3 items-center">
                    <div class="bg-[#d32340] text-white rounded-xl w-12 h-12 grid place-items-center text-3xl">
                        <i class="fi fi-rr-heart" aria-hidden="true"></i>
                    </div>
                    <div>
                        <strong class="text-xl">{{ __('Manda Blood Donation Program') }}</strong>
                        <p class="text-xs text-gray-500">{{ __('A drop of blood, a new life') }}</p>
                    </div>
                </a>
                <nav class="hidden md:flex gap-7 text-sm font-semibold">
                    <a href="#home">{{ __('Home') }}</a>
                    <a href="#donors">{{ __('Find donors') }}</a>
                    <a href="#about">{{ __('About us') }}</a>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    @include('partials.language-switcher')
                    <button class="btn" onclick="showModal('register')">
                        {{ __('Become a donor') }}
                    </button>
                </div>
            </div>
        </header>
        <section id="home" class="hero">
            <div class="max-w-7xl mx-auto px-5 py-20 grid md:grid-cols-2 gap-12 items-center">
                <div class="reveal">
                    <span class="bg-red-50 red rounded-full px-4 py-2 font-semibold text-sm"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Together in service of humanity') }}</span>
                    <h1 class="text-4xl md:text-6xl font-bold leading-tight mt-7">{{ __('Your blood') }} <span class="red">{{ __('Can save') }}</span> {{ __('A precious life') }}</h1>
                    <p class="text-gray-600 text-lg leading-8 my-7">{{ __('Connecting blood donors in Manda and nearby areas. Find a suitable donor when you need one.') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <a class="btn" href="#donors">{{ __('Find donors →') }}</a>
                        <button onclick="showModal('register')" class="border bg-white px-6 rounded-xl font-bold">
                            {{ __('I want to donate blood') }}
                        </button>
                    </div>
                </div>
                <div class="reveal flex justify-center">
                    <div class="bg-white rounded-full border-[20px] border-red-50 shadow-xl w-80 h-80 md:w-[420px] md:h-[420px] flex flex-col justify-center items-center text-center">
                        <div class="text-[#d32340] text-[130px] leading-none animate__animated animate__pulse animate__infinite animate__slow">
                            <i class="fi fi-rr-heart" aria-hidden="true"></i>
                        </div>
                        <h2 class="font-bold text-2xl">{{ __('Donate blood, save lives') }}</h2>
                        <p class="text-gray-500 mt-3">{{ __('Your support is someone\'s hope for survival') }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-5 -mt-7 relative">
            <div class="card grid grid-cols-2 md:grid-cols-4 text-center gap-5">
                <div>
                    <b id="donorTotal" class="text-3xl red">—</b>
                    <p class="text-gray-500">{{ __('Available donors') }}</p>
                </div>
                <div>
                    <b id="groupTotal" class="text-3xl red">—</b>
                    <p class="text-gray-500">{{ __('Blood group') }}</p>
                </div>
                <div>
                    <b class="text-3xl red">{{ __('24/7') }}</b>
                    <p class="text-gray-500">{{ __('Search') }}</p>
                </div>
                <div>
                    <b class="text-3xl red">{{ __('100') }}%</b>
                    <p class="text-gray-500">{{ __('Volunteer initiative') }}</p>
                </div>
            </div>
        </section>
        <section id="donors" class="max-w-7xl mx-auto px-5 py-20">
            <div class="text-center mb-9">
                <span class="red font-bold">{{ __('Donor directory') }}</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-3">{{ __('Find the help') }} <span class="red">{{ __('Find donors') }}</span></h2>
                <p class="text-gray-500 mt-3">{{ __('Search easily by blood group and area') }}</p>
            </div>
            <div class="card grid md:grid-cols-4 gap-4">
                <select id="blood" class="field">
                    <option value="">{{ __('All blood groups') }}</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                    <option>O+</option>
                    <option>O-</option>
                </select>
                <select id="area" class="field">
                    <option value="">{{ __('All areas') }}</option>
                </select>
                <input id="query" class="field" placeholder="{{ __('Enter donor name') }}">
                <button class="btn" id="searchBtn">
                    {{ __('Search') }}
                </button>
            </div>
            <div class="flex justify-between mt-10 mb-5">
                <h3 class="font-bold text-xl">{{ __('Donor list') }}</h3>
                <span id="result" class="text-gray-500"></span>
            </div>
            <div id="grid" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            </div>
            <p class="text-gray-400 text-xs mt-5">* {{ __('The displayed names and details are fictional. They do not represent real donors.') }}</p>
        </section>
        <section id="about" class="bg-[#fff1f3] py-20">
            <div class="max-w-7xl mx-auto px-5 grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="red font-bold">{{ __('Our mission') }}</span>
                    <h2 class="text-4xl font-bold my-5">{{ __('So a lack of blood') }} <span class="red">{{ __('Let no life stop') }}</span></h2>
                    <p class="text-gray-600 leading-8">{{ __('Manda Blood Donation Program is a volunteer platform for donor registration, searching by blood group and area, and consent-based contact.') }}</p>
                    <button class="btn mt-6" onclick="showModal('register')">
                        {{ __('Register') }}
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="card">
                        <div class="text-4xl red">
                            <i class="fi fi-rr-heart" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Humanitarian support') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Support in emergencies') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            ⌕
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Easy search') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('By area and blood group') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            ✦
                        </div>
                        <h3 class="font-bold mt-4">{{ __('Volunteer initiative') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Together for humanity') }}</p>
                    </div>
                    <div class="card">
                        <div class="text-4xl red">
                            ✓
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
        <footer class="bg-[#192333] text-white">
            <div class="max-w-7xl mx-auto px-5 py-12 grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-xl"><i class="fi fi-rr-heart" aria-hidden="true"></i> {{ __('Manda Blood Donation Program') }}</h3>
                    <p class="text-gray-400 mt-3">{{ __('Donate blood, save lives.') }}</p>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('Quick links') }}</h3>
                    <p class="text-gray-400 mt-3"><a href="#donors">{{ __('Find donors') }}</a> · <a href="#about">{{ __('About us') }}</a></p>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('Address') }}</h3>
                    <p class="text-gray-400 mt-3">{{ __('Manda, Naogaon, Bangladesh') }}</p>
                </div>
            </div>
            <div class="border-t border-white/10 text-center text-gray-400 py-5 text-sm">
                © {{ __('2026 Manda Blood Donation Program') }}
            </div>
        <div class="px-5 pb-4 text-center"><a href="https://www.flaticon.com/uicons" class="icon-credit" target="_blank" rel="noopener noreferrer">UIcons by Flaticon</a></div>
        </footer>
        <div id="modal" class="hidden fixed inset-0 z-50 bg-black/70 p-4 items-center justify-center">
            <div class="bg-white rounded-2xl p-7 max-w-md w-full relative">
                <button onclick="hideModal()" class="absolute right-5 top-4 text-2xl">
                    ×
                </button>
                <h2 id="modalTitle" class="text-2xl font-bold mb-3">{{ __('Donor registration') }}</h2>
                <p id="modalDesc" class="text-gray-500 text-sm mb-5">{{ __('Complete the details below') }}</p>
                <form id="form" class="space-y-3">
                    <input name="name" required class="field" placeholder="{{ __('Full name') }}">
                    <input name="phone" required class="field" placeholder="{{ __('Mobile number') }}" inputmode="tel">
                    <select name="blood_group_id" required class="field">
                        <option value="">{{ __('Blood group') }}</option>
                        <option>A+</option>
                        <option>A-</option>
                        <option>B+</option>
                        <option>B-</option>
                        <option>AB+</option>
                        <option>AB-</option>
                        <option>O+</option>
                        <option>O-</option>
                    </select>
                    <input name="area" required class="field" placeholder="{{ __('Address / area') }}">
<textarea name="message" class="field" placeholder="{{ __('Reason for contact') }}" hidden></textarea>
                    <label class="text-sm flex gap-2">
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
@vite('resources/js/app.js')

    </body>
</html>


