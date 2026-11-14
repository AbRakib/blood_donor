@extends('admin.layouts.admin')
@section('page', 'vehicles')
@section('title', $vehicle->exists ? __('Edit vehicle') : __('Add vehicle'))
@section('content')
<section class="panel settings-main">
    <form method="POST" action="{{ $vehicle->exists ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store') }}" class="settings-section">
        @csrf
        @if($vehicle->exists) @method('PUT') @endif
        <div class="settings-section-body">
            @if($errors->any())
                <div role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @if($drivers->isEmpty())
                <p role="status">{{ __('An active driver must be available before adding a vehicle.') }}</p>
            @endif
            <div class="settings-fields">
                <div class="field">
                    <label for="driver_id">{{ __('Driver') }}</label>
                    <select id="driver_id" name="driver_id" required>
                        <option value="">{{ __('Select driver') }}</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected(old('driver_id', $vehicle->driver_id) == $driver->id)>{{ $driver->name }} — {{ $driver->phone }}</option>
                        @endforeach
                    </select>
                </div>
                @foreach(['vehicle_type' => 'Vehicle type', 'vehicle_number' => 'Vehicle number', 'vehicle_model' => 'Model', 'vehicle_color' => 'Color'] as $name => $label)
                    <div class="field">
                        <label for="{{ $name }}">{{ __($label) }}</label>
                        <div class="settings-input"><input id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $vehicle->{$name}) }}" maxlength="255" @required(in_array($name, ['vehicle_type', 'vehicle_number']))></div>
                    </div>
                @endforeach
                <div class="field">
                    <label for="status">{{ __('Status') }}</label>
                    <select id="status" name="status" required>
                        <option value="1" @selected(old('status', $vehicle->status ?? 1) == 1)>{{ __('Active') }}</option>
                        <option value="0" @selected(old('status', $vehicle->status ?? 1) == 0)>{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
            <button class="btn primary" type="submit" @disabled($drivers->isEmpty())>{{ __('Save vehicle') }}</button>
            <a class="btn" href="{{ route('admin.vehicles.index') }}">{{ __('Cancel') }}</a>
        </div>
    </form>
</section>
@endsection
