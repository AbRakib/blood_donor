<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ __('Manda Blood Donation Program') }}</title>
    <link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
    @include('partials.translations')
</head>

<body data-donation-page="dashboard" data-admin-page="@yield('page', 'dashboard')">
    @include('partials.preloader')
    @php($currentPage = trim($__env->yieldContent('page', 'dashboard')))
    <div class="app">
        <aside class="side" id="sidebar">
            <div class="brand"><img class="sidebar-brand-image"
                    src="{{ asset('images/manda-blood-donation-logo.png') }}" alt="Manda Blood Donation Program"></div>

            <nav class="nav" id="nav">
                <a href="{{ route('dashboard') }}" @class(['active' => $currentPage === 'dashboard'])
                    @if ($currentPage === 'dashboard') aria-current="page" @endif><i class="fi fi-rr-home"
                        aria-hidden="true"></i> {{ __('Dashboard') }}</a>
                <a href="{{ route('admin.blood-donors.index') }}" @class(['active' => $currentPage === 'donors'])
                    @if ($currentPage === 'donors') aria-current="page" @endif><i class="fi fi-rr-users"
                        aria-hidden="true"></i> {{ __('Donor list') }}</a>
                <a href="{{ route('admin.blood-requests.index') }}" @class(['active' => $currentPage === 'requests'])
                    @if ($currentPage === 'requests') aria-current="page" @endif><i class="fi fi-rr-clipboard-list"
                        aria-hidden="true"></i> {{ __('Blood requests') }} <span class="count"
                        id="openRequestCount">{{ __('0') }}</span></a>
                <a href="{{ route('admin.blood-groups.index') }}" @class(['active' => $currentPage === 'groups'])
                    @if ($currentPage === 'groups') aria-current="page" @endif><i class="fi fi-rr-blood"
                        aria-hidden="true"></i> {{ __('Blood group') }}</a>
                <a href="{{ route('admin.donations.index') }}" @class(['active' => $currentPage === 'donations'])
                    @if ($currentPage === 'donations') aria-current="page" @endif><i class="fi fi-rr-calendar"
                        aria-hidden="true"></i> {{ __('Donation history') }}</a>
            </nav>
            <div class="section-label" style="margin-top:26px">{{ __('Management') }}</div>
            <nav class="nav">
                <a href="{{ route('admin.drivers.index') }}" @class(['active' => $currentPage === 'drivers'])
                    @if ($currentPage === 'drivers') aria-current="page" @endif><i class="fi fi-rr-user" aria-hidden="true"></i> {{ __('Drivers') }}</a>
                <a href="{{ route('admin.vehicles.index') }}" @class(['active' => $currentPage === 'vehicles'])
                    @if ($currentPage === 'vehicles') aria-current="page" @endif><i class="fi fi-rr-car" aria-hidden="true"></i> {{ __('Vehicles') }}</a>
                <a href="{{ route('admin.reports.index') }}" @class(['active' => $currentPage === 'reports'])
                    @if ($currentPage === 'reports') aria-current="page" @endif><i class="fi fi-rr-chart-histogram"
                        aria-hidden="true"></i> {{ __('Reports and statistics') }}</a>
                <a href="{{ route('admin.settings.index') }}" @class(['active' => $currentPage === 'settings'])
                    @if ($currentPage === 'settings') aria-current="page" @endif><i class="fi fi-rr-settings-sliders"
                        aria-hidden="true"></i> {{ __('Settings') }}</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="adminmini">
                    <div class="avatar">AD</div>
                    <div><strong>{{ __('Admin panel') }}</strong><small>{{ __('Super admin') }}</small></div><i
                        class="fi fi-rr-angle-double-small-down" aria-hidden="true"
                        style="margin-left:auto;color:#99a3ad;width:15px"></i>
                </div>
            </div>
        </aside>
        <main class="main">
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:14px"><button class="iconbtn mobile-menu"
                        aria-label="{{ __('Main menu') }}"
                        onclick="if(window.innerWidth > 950){document.body.classList.toggle('sidebar-collapsed')}else{document.getElementById('sidebar').classList.toggle('open')}"
                        style="border:0;background:none"><i class="fi fi-rr-menu-burger"
                            aria-hidden="true"></i></button><label class="search top-search"><i class="fi fi-rr-search"
                            aria-hidden="true"></i><input id="globalSearch"
                            placeholder="{{ __('Search by name, area or mobile') }}..."
                            aria-label="{{ __('Search by name, area or mobile') }}"></label>
                    <div class="breadcrumbs" hidden>{{ __('Page /') }} <span id="crumb">@yield('title')</span>
                    </div>
                </div>
                <div class="top-actions">@include('partials.language-switcher')<div class="date-display"><i
                            class="fi fi-rr-calendar" aria-hidden="true"></i>
                        <div><span id="today"></span><small id="weekday"></small></div>
                    </div><button class="iconbtn" onclick="showToast('{{ __('No new notifications') }}')"><i
                            class="fi fi-rr-bell" aria-hidden="true"></i><span class="dot"></span></button>
                    <details class="account-dropdown" id="accountDropdown">
                        <summary class="userline" aria-label="{{ __('Account') }}">
                            <span class="avatar">AD</span>
                            <span class="account-name"><b>{{ auth()->user()->name }}</b><small>{{ __('Administrator') }}</small></span>
                        </summary>
                        <div class="account-menu">
                            <button type="button" id="showAccountProfile">{{ __('Profile') }}</button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">{{ __('Log out') }}</button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>
            <div class="content">
                @unless (View::hasSection('hide-heading'))
                    <div class="heading">
                        <div>
                            <h1 id="pageTitle">@yield('title')</h1>
                            <p id="pageSubtitle">@yield('subtitle')</p>
                        </div>
                        @yield('actions')
                    </div>
                @endunless
                @yield('content')
            </div>
        </main>
    </div>
    <dialog id="accountProfile" class="account-profile" aria-labelledby="accountProfileTitle">
        <div class="modal-header">
            <h2 id="accountProfileTitle">{{ __('Profile') }}</h2>
            <form method="dialog"><button class="close" aria-label="{{ __('Close') }}">×</button></form>
        </div>
        <dl>
            <dt>{{ __('Name') }}</dt><dd>{{ auth()->user()->name }}</dd>
            <dt>{{ __('Email') }}</dt><dd>{{ auth()->user()->email }}</dd>
        </dl>
    </dialog>
    @include('admin.partials.admin-modal')
    <div class="toast" id="toast"></div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.donationRoutes =
            {{ Illuminate\Support\Js::from([
                'groups' => route('blood-groups.index'),
                'locations' => route('locations.index'),
                'donors' => route('donors.index'),
                'register' => route('donors.store'),
                'admin' => route('admin.dashboard'),
                'adminDonors' => route('admin.blood-donors.index'),
                'requests' => route('admin.blood-requests.index'),
                'donations' => route('admin.donations.index'),
                'settings' => route('admin.settings.index'),
                'reports' => route('admin.reports.index'),
                'login' => route('login'),
            ]) }};
    </script>
    <script>
        window.donationPages =
            {{ Illuminate\Support\Js::from([
                'dashboard' => route('dashboard'),
                'donors' => route('admin.blood-donors.index'),
                'requests' => route('admin.blood-requests.index'),
                'groups' => route('admin.blood-groups.index'),
                'donations' => route('admin.donations.index'),
                'reports' => route('admin.reports.index'),
                'settings' => route('admin.settings.index'),
            ]) }};
    </script>
    <script type="module" src="{{ asset('backend/js/main.js') }}"></script>

</body>

</html>
