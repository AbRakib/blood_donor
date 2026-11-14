@extends('admin.layouts.admin')
@section('page', 'drivers')
@section('title', __('Driver details'))
@section('content')
<section class="panel settings-main">
    <div class="settings-section"><div class="settings-section-body">
        <dl>
            @foreach(['name' => 'Name', 'phone' => 'Phone', 'email' => 'Email', 'date_of_birth' => 'Date of birth', 'gender' => 'Gender', 'present_address' => 'Present address', 'permanent_address' => 'Permanent address', 'city' => 'City', 'district' => 'District', 'nid_number' => 'NID number', 'license_number' => 'License number', 'license_type' => 'License type', 'license_issue_date' => 'License issue date', 'license_expiry_date' => 'License expiry date'] as $name => $label)
                <dt>{{ __($label) }}</dt><dd>{{ ($driver->{$name} instanceof \DateTimeInterface ? $driver->{$name}->format('Y-m-d') : $driver->{$name}) ?? '—' }}</dd>
            @endforeach
            <dt>{{ __('Status') }}</dt><dd>{{ $driver->status ? __('Active') : __('Inactive') }}</dd>
        </dl>
        <a class="btn primary" href="{{ route('admin.drivers.edit', $driver) }}">{{ __('Edit driver') }}</a>
        <a class="btn" href="{{ route('admin.drivers.index') }}">{{ __('Back to drivers') }}</a>
    </div></div>
</section>
@endsection
