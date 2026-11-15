@extends('admin.layouts.admin')
@section('page', 'drivers')
@section('title', $driver->exists ? __('Edit driver') : __('Add driver'))
@section('subtitle', __('Manage driver contact details and driving credentials.'))
@section('actions')
<a class="btn" href="{{ route('admin.drivers.index') }}"><i class="fi fi-rr-arrow-left" aria-hidden="true"></i> {{ __('Back to drivers') }}</a>
@endsection
@section('content')
<style>
    .content:has(> .driver-form) > .heading { max-width: 1100px; margin-left: auto; margin-right: auto; }
    .driver-form { max-width: 1100px; margin: 0 auto; }
    .driver-card-icon { display: grid; place-items: center; flex: none; color: var(--red); background: #fff1f2; border-radius: 12px; width: 46px; height: 46px; font-size: 20px; }
    .driver-card-head p { margin: 0; color: #71808e; font-size: 13px; }
    .driver-form-layout { display: grid; grid-template-columns: minmax(0, 1fr); align-items: start; gap: 22px; }
    .driver-form-sections { display: grid; gap: 20px; min-width: 0; }
    .driver-card { border: 1px solid var(--line); border-radius: 14px; background: #fff; overflow: hidden; }
    .driver-card-head { display: flex; gap: 12px; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f2f4; }
    .driver-card-icon { width: 36px; height: 36px; border-radius: 10px; font-size: 16px; }
    .driver-card-head h3 { margin: 0 0 3px; font-size: 15px; }
    .driver-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; padding: 24px; }
    .driver-fields .field { align-content: start; min-width: 0; }
    .driver-floating { position: relative; }
    .driver-form .driver-floating label { position: absolute; top: 22px; left: 12px; max-width: calc(100% - 48px); padding: 0 5px; background: #fff; color: #71808e; font-size: 13px; font-weight: 500; line-height: 18px; transform: translateY(-50%); transform-origin: left center; cursor: text; transition: top .15s ease, font-size .15s ease, color .15s ease; }
    .driver-floating label .fi { font-size: 12px; margin-right: 4px; }
    .driver-form .driver-floating:focus-within label,
    .driver-form .driver-floating.is-filled label,
    .driver-form .driver-floating:has(input[placeholder]:not(:placeholder-shown), textarea:not(:placeholder-shown)) label,
    .driver-form .driver-floating:has(input:autofill) label { top: 0; font-size: 11px; color: #4b5663; }
    .driver-form .driver-floating:focus-within label { color: var(--red); }
    .driver-form .driver-floating input[type="date"] { color: transparent; }
    .driver-form .driver-floating:focus-within input[type="date"],
    .driver-form .driver-floating.is-filled input[type="date"] { color: var(--dark); }
    .driver-form .driver-floating input[type="date"]::-webkit-datetime-edit { opacity: 0; }
    .driver-form .driver-floating:focus-within input[type="date"]::-webkit-datetime-edit,
    .driver-form .driver-floating.is-filled input[type="date"]::-webkit-datetime-edit { opacity: 1; }
    @media (prefers-reduced-motion: reduce) { .driver-form .driver-floating label { transition: none; } }
    .driver-form .field input, .driver-form .field select, .driver-form .field textarea { width: 100%; min-height: 44px; border: 1px solid #d8e2ee; border-radius: 8px; padding: 10px 12px; background: #fff; color: var(--dark); font: inherit; outline: none; transition: border-color .15s, box-shadow .15s; }
    .driver-form .field textarea { resize: vertical; }
    .driver-form .field :is(input, select, textarea):focus { border-color: var(--red); box-shadow: 0 0 0 3px #e33c4910; }
    .driver-form .field [aria-invalid="true"] { border-color: var(--red); }
    .driver-required, .driver-error { color: #c92d3b; }
    .driver-error { font-size: 12px; }
    .driver-form-footer { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 24px; margin-top: 22px; }
    .driver-form-footer p { color: #71808e; font-size: 12px; margin: 0; }
    .driver-form-actions { display: flex; gap: 10px; }
    .driver-form .btn { text-decoration: none; justify-content: center; }
    .driver-validation { border: 1px solid #f7cbd0; border-radius: 10px; background: #fff5f5; color: #b52532; padding: 14px 18px; margin-bottom: 20px; }
    @media (max-width: 600px) { .driver-fields { grid-template-columns: minmax(0, 1fr); padding: 18px; gap: 16px; } .driver-card-head { padding: 18px; } .driver-form-footer { align-items: stretch; flex-direction: column; padding: 18px; } .driver-form-actions .btn { flex: 1; } }
</style>
<form method="POST" action="{{ $driver->exists ? route('admin.drivers.update', $driver) : route('admin.drivers.store') }}" class="driver-form">
    @csrf
    <input type="hidden" name="status" value="{{ $driver->status ?? 1 }}">
    @if($driver->exists) @method('PUT') @endif
    @if($errors->any())
        <div class="driver-validation" role="alert">{{ __('Please check the highlighted fields and try again.') }}</div>
    @endif
    <div class="driver-form-layout">
        <div class="driver-form-sections">
            <section class="driver-card" aria-labelledby="driver-section-0">
                <div class="driver-card-head">
                    <span class="driver-card-icon"><i class="fi fi-rr-user" aria-hidden="true"></i></span>
                    <div><h3 id="driver-section-0">{{ __('Personal details') }}</h3><p>{{ __('Contact information to help you reach this driver.') }}</p></div>
                </div>
                <div class="driver-fields">
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @if($errors->has('name')) aria-describedby="name-error" @endif name="name" autocomplete="name" value="{{ old('name', $driver->name) }}" required maxlength="255">
                        <label for="name"><i class="fi fi-rr-user" aria-hidden="true"></i> {{ __('Name') }} <span class="driver-required" aria-hidden="true">*</span></label>
                    </div>
                    @error('name')<small class="driver-error" id="name-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="tel" id="phone" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" @if($errors->has('phone')) aria-describedby="phone-error" @endif name="phone" autocomplete="tel" value="{{ old('phone', $driver->phone) }}" required maxlength="20">
                        <label for="phone"><i class="fi fi-rr-phone-call" aria-hidden="true"></i> {{ __('Phone') }} <span class="driver-required" aria-hidden="true">*</span></label>
                    </div>
                    @error('phone')<small class="driver-error" id="phone-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="email" id="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @if($errors->has('email')) aria-describedby="email-error" @endif name="email" autocomplete="email" value="{{ old('email', $driver->email) }}" maxlength="255">
                        <label for="email"><i class="fi fi-rr-envelope" aria-hidden="true"></i> {{ __('Email') }}</label>
                    </div>
                    @error('email')<small class="driver-error" id="email-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input type="date" id="date_of_birth" aria-invalid="{{ $errors->has('date_of_birth') ? 'true' : 'false' }}" @if($errors->has('date_of_birth')) aria-describedby="date_of_birth-error" @endif name="date_of_birth" autocomplete="bday" value="{{ old('date_of_birth', $driver->date_of_birth?->format('Y-m-d')) }}">
                        <label for="date_of_birth"><i class="fi fi-rr-calendar" aria-hidden="true"></i> {{ __('Date of birth') }}</label>
                    </div>
                    @error('date_of_birth')<small class="driver-error" id="date_of_birth-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <select id="gender" aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}" @if($errors->has('gender')) aria-describedby="gender-error" @endif name="gender">
                        <option value=""></option>
                        @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('gender', $driver->gender) === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                        <label for="gender"><i class="fi fi-rr-user" aria-hidden="true"></i> {{ __('Gender') }}</label>
                    </div>
                    @error('gender')<small class="driver-error" id="gender-error">{{ $message }}</small>@enderror
                </div>
                </div>
            </section>
            <section class="driver-card" aria-labelledby="driver-section-1">
                <div class="driver-card-head">
                    <span class="driver-card-icon"><i class="fi fi-rr-marker" aria-hidden="true"></i></span>
                    <div><h3 id="driver-section-1">{{ __('Address information') }}</h3><p>{{ __('Where the driver currently lives and their permanent address.') }}</p></div>
                </div>
                <div class="driver-fields">
                <div class="field">
                    <div class="driver-floating">
                        <textarea placeholder=" " id="present_address" aria-invalid="{{ $errors->has('present_address') ? 'true' : 'false' }}" @if($errors->has('present_address')) aria-describedby="present_address-error" @endif name="present_address" autocomplete="street-address" maxlength="5000" rows="3">{{ old('present_address', $driver->present_address) }}</textarea>
                        <label for="present_address"><i class="fi fi-rr-marker" aria-hidden="true"></i> {{ __('Present address') }}</label>
                    </div>
                    @error('present_address')<small class="driver-error" id="present_address-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <textarea placeholder=" " id="permanent_address" aria-invalid="{{ $errors->has('permanent_address') ? 'true' : 'false' }}" @if($errors->has('permanent_address')) aria-describedby="permanent_address-error" @endif name="permanent_address" maxlength="5000" rows="3">{{ old('permanent_address', $driver->permanent_address) }}</textarea>
                        <label for="permanent_address"><i class="fi fi-rr-marker" aria-hidden="true"></i> {{ __('Permanent address') }}</label>
                    </div>
                    @error('permanent_address')<small class="driver-error" id="permanent_address-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="city" aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}" @if($errors->has('city')) aria-describedby="city-error" @endif name="city" value="{{ old('city', $driver->city) }}" maxlength="255">
                        <label for="city"><i class="fi fi-rr-building" aria-hidden="true"></i> {{ __('City') }}</label>
                    </div>
                    @error('city')<small class="driver-error" id="city-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="district" aria-invalid="{{ $errors->has('district') ? 'true' : 'false' }}" @if($errors->has('district')) aria-describedby="district-error" @endif name="district" value="{{ old('district', $driver->district) }}" maxlength="255">
                        <label for="district"><i class="fi fi-rr-marker" aria-hidden="true"></i> {{ __('District') }}</label>
                    </div>
                    @error('district')<small class="driver-error" id="district-error">{{ $message }}</small>@enderror
                </div>
                </div>
            </section>
            <section class="driver-card" aria-labelledby="driver-section-2">
                <div class="driver-card-head">
                    <span class="driver-card-icon"><i class="fi fi-rr-id-badge" aria-hidden="true"></i></span>
                    <div><h3 id="driver-section-2">{{ __('Identity & license') }}</h3><p>{{ __('Keep identification and driving credentials together.') }}</p></div>
                </div>
                <div class="driver-fields">
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="nid_number" aria-invalid="{{ $errors->has('nid_number') ? 'true' : 'false' }}" @if($errors->has('nid_number')) aria-describedby="nid_number-error" @endif name="nid_number" value="{{ old('nid_number', $driver->nid_number) }}" maxlength="255">
                        <label for="nid_number"><i class="fi fi-rr-id-badge" aria-hidden="true"></i> {{ __('NID number') }}</label>
                    </div>
                    @error('nid_number')<small class="driver-error" id="nid_number-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="license_number" aria-invalid="{{ $errors->has('license_number') ? 'true' : 'false' }}" @if($errors->has('license_number')) aria-describedby="license_number-error" @endif name="license_number" value="{{ old('license_number', $driver->license_number) }}" maxlength="255">
                        <label for="license_number"><i class="fi fi-rr-id-badge" aria-hidden="true"></i> {{ __('License number') }}</label>
                    </div>
                    @error('license_number')<small class="driver-error" id="license_number-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input placeholder=" " type="text" id="license_type" aria-invalid="{{ $errors->has('license_type') ? 'true' : 'false' }}" @if($errors->has('license_type')) aria-describedby="license_type-error" @endif name="license_type" value="{{ old('license_type', $driver->license_type) }}" maxlength="255">
                        <label for="license_type"><i class="fi fi-rr-car" aria-hidden="true"></i> {{ __('License type') }}</label>
                    </div>
                    @error('license_type')<small class="driver-error" id="license_type-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input type="date" id="license_issue_date" aria-invalid="{{ $errors->has('license_issue_date') ? 'true' : 'false' }}" @if($errors->has('license_issue_date')) aria-describedby="license_issue_date-error" @endif name="license_issue_date" value="{{ old('license_issue_date', $driver->license_issue_date?->format('Y-m-d')) }}">
                        <label for="license_issue_date"><i class="fi fi-rr-calendar" aria-hidden="true"></i> {{ __('License issue date') }}</label>
                    </div>
                    @error('license_issue_date')<small class="driver-error" id="license_issue_date-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <div class="driver-floating">
                        <input type="date" id="license_expiry_date" aria-invalid="{{ $errors->has('license_expiry_date') ? 'true' : 'false' }}" @if($errors->has('license_expiry_date')) aria-describedby="license_expiry_date-error" @endif name="license_expiry_date" value="{{ old('license_expiry_date', $driver->license_expiry_date?->format('Y-m-d')) }}">
                        <label for="license_expiry_date"><i class="fi fi-rr-calendar" aria-hidden="true"></i> {{ __('License expiry date') }}</label>
                    </div>
                    @error('license_expiry_date')<small class="driver-error" id="license_expiry_date-error">{{ $message }}</small>@enderror
                </div>
                </div>
            </section>
        </div>
    </div>
    <div class="driver-card driver-form-footer">
        <p><span class="driver-required" aria-hidden="true">*</span> {{ __('Name and phone number are required.') }}</p>
        <div class="driver-form-actions">
            <a class="btn" href="{{ route('admin.drivers.index') }}">{{ __('Cancel') }}</a>
            <button class="btn primary" type="submit"><i class="fi fi-rr-check" aria-hidden="true"></i> {{ __('Save driver') }}</button>
        </div>
    </div>
</form>
<script>
    document.querySelectorAll('.driver-floating').forEach((field) => {
        const input = field.querySelector('input, select, textarea');
        const updateLabel = () => field.classList.toggle('is-filled', input.value !== '');
        input.addEventListener('input', updateLabel);
        input.addEventListener('change', updateLabel);
        window.addEventListener('pageshow', updateLabel);
        updateLabel();
    });
</script>
@endsection
