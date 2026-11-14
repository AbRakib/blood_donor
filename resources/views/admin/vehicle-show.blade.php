@extends('admin.layouts.admin')
@section('page', 'vehicles')
@section('title', __('Vehicle details'))
@section('content')
<section class="panel settings-main">
    <div class="settings-section"><div class="settings-section-body">
        <dl>
            @foreach(['vehicle_number' => 'Vehicle number', 'vehicle_type' => 'Vehicle type', 'vehicle_model' => 'Model', 'vehicle_color' => 'Color'] as $name => $label)
                <dt>{{ __($label) }}</dt><dd>{{ $vehicle->{$name} ?? '—' }}</dd>
            @endforeach
            <dt>{{ __('Driver') }}</dt><dd>{{ $vehicle->driver?->name ?? '—' }}</dd>
            <dt>{{ __('Phone') }}</dt><dd>{{ $vehicle->driver?->phone ?? '—' }}</dd>
            <dt>{{ __('Status') }}</dt><dd>{{ $vehicle->status ? __('Active') : __('Inactive') }}</dd>
        </dl>
        <a class="btn primary" href="{{ route('admin.vehicles.edit', $vehicle) }}">{{ __('Edit vehicle') }}</a>
        <a class="btn" href="{{ route('admin.vehicles.index') }}">{{ __('Back to vehicles') }}</a>
    </div></div>
</section>
@endsection
