@extends('admin.layouts.admin')

@section('page', 'settings')
@section('title', __('Settings'))
@section('hide-heading', 'true')

@section('content')
<section id="settingsView">
    <form id="settingsForm" class="settings-layout" action="{{ route('admin.settings.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="status" value="{{ (int) ($setting?->status ?? 1) }}">
        <div class="panel settings-main">
            <section class="settings-section">
                <div class="settings-section-icon"><i class="fi fi-rr-settings" aria-hidden="true"></i></div>
                <div class="settings-section-body">
                    <h2>{{ __('General information') }}</h2>
                    <p>{{ __('Basic information about your blood donation platform.') }}</p>
                    <div class="settings-fields">
                        @include('admin.partials.settings-field', ['name' => 'name', 'label' => 'Site name', 'type' => 'text', 'icon' => 'building'])
                        @include('admin.partials.settings-field', ['name' => 'email', 'label' => 'Contact email', 'type' => 'email', 'icon' => 'envelope'])
                        @include('admin.partials.settings-field', ['name' => 'phone', 'label' => 'Mobile number', 'type' => 'tel', 'icon' => 'phone-call'])
                        @include('admin.partials.settings-field', ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'icon' => 'marker'])
                    </div>
                </div>
            </section>
            <section class="settings-section">
                <div class="settings-section-icon"><i class="fi fi-rr-picture" aria-hidden="true"></i></div>
                <div class="settings-section-body">
                    <h2>{{ __('Branding') }}</h2>
                    <p>{{ __('Upload your logo and site icon. Recommended size: PNG, JPG, WebP or GIF (Max 2 MB).') }}</p>
                    <div class="settings-fields settings-branding-fields">
                        @include('admin.partials.settings-upload', ['name' => 'logo', 'label' => 'Logo (Header)'])
                        @include('admin.partials.settings-upload', ['name' => 'footer_logo', 'label' => 'Footer logo'])
                        @include('admin.partials.settings-upload', ['name' => 'meta_icon', 'label' => 'Website icon (Favicon)', 'uploadLabel' => 'Click to upload icon'])
                    </div>
                </div>
            </section>
            <section class="settings-section">
                <div class="settings-section-icon"><i class="fi fi-rr-share" aria-hidden="true"></i></div>
                <div class="settings-section-body">
                    <h2>{{ __('Social media links') }}</h2>
                    <p>{{ __('Add your social media profiles. Leave empty if not available.') }}</p>
                    <div class="settings-fields">
                        @include('admin.partials.settings-field', ['name' => 'facebook', 'label' => 'Facebook', 'type' => 'url', 'icon' => 'globe', 'placeholder' => 'https://facebook.com/yourpage'])
                        @include('admin.partials.settings-field', ['name' => 'twitter', 'label' => 'Twitter / X', 'type' => 'url', 'icon' => 'globe', 'placeholder' => 'https://x.com/yourprofile'])
                        @include('admin.partials.settings-field', ['name' => 'instagram', 'label' => 'Instagram', 'type' => 'url', 'icon' => 'camera', 'placeholder' => 'https://instagram.com/yourprofile'])
                        @include('admin.partials.settings-field', ['name' => 'linkedin', 'label' => 'LinkedIn', 'type' => 'url', 'icon' => 'globe', 'placeholder' => 'https://linkedin.com/company/yourpage'])

                    </div>
                </div>
            </section>
        </div>
        <aside class="settings-aside">
            <section class="panel settings-preview">
                <div class="settings-card-heading"><span><i class="fi fi-rr-eye" aria-hidden="true"></i></span><div><h2>{{ __('Preview') }}</h2><p>{{ __('How your site information will appear.') }}</p></div></div>
                <div class="settings-preview-site">
                    <span class="settings-drop" aria-hidden="true"><i class="fi fi-rr-heart" aria-hidden="true"></i></span>
                    <img id="settingsLiveLogo" alt="{{ __('Logo') }}" @if($setting?->logo_url) src="{{ $setting->logo_url }}" @else hidden @endif>
                    <strong id="settingsLiveName">{{ $setting?->name ?: __('Blood Donation') }}</strong>
                    <p>{{ __('A group of blood, a new life') }}</p>
                </div>
            </section>
            <section class="panel settings-guidelines">
                <div class="settings-card-heading"><span><i class="fi fi-rr-document" aria-hidden="true"></i></span><h2>{{ __('Upload guidelines') }}</h2></div>
                <ul>
                    @foreach(['Use PNG, JPG, WebP or GIF format', 'Maximum file size: 2 MB', 'For best result, use transparent background', 'Recommended size: 200 × 60 px (logo)', 'Recommended size: 32 × 32 px (favicon)'] as $guideline)
                        <li><i class="fi fi-rr-check-circle" aria-hidden="true"></i> {{ __($guideline) }}</li>
                    @endforeach
                </ul>
            </section>
            <div class="panel settings-actions">
                <button class="btn primary" type="submit"><i class="fi fi-rr-disk" aria-hidden="true"></i> {{ __('Save settings') }}</button>
                <p class="settings-feedback" role="status" aria-live="polite"></p>
            </div>
        </aside>
    </form>
</section>
@endsection
