<form method="POST"
    action="{{ $vehicle->exists ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store') }}"
    id="{{ $formId }}">
    @csrf
    @if ($vehicle->exists)
        @method('PUT')
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
    @endif
    @if (
        $errors->any() &&
            ((!$vehicle->exists && !old('_method')) ||
                ($vehicle->exists &&
                    (string) old('_method') === 'PUT' &&
                    (string) old('vehicle_id') === (string) $vehicle->id)))
        <div role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($drivers->isEmpty())
        <p role="status">{{ __('An active driver must be available before adding a vehicle.') }}</p>
    @endif
    <div class="settings-fields">
        <div class="field">
            <label for="driver_id_{{ $formId }}">{{ __('Driver') }}</label>
            <select id="driver_id_{{ $formId }}" name="driver_id" required>
                <option value="">{{ __('Select driver') }}</option>
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}" @selected(old('driver_id', $vehicle->driver_id) == $driver->id)>{{ $driver->name }} —
                        {{ $driver->phone }}</option>
                @endforeach
                @if ($vehicle->driver && $drivers->doesntContain('id', $vehicle->driver_id))
                    <option value="{{ $vehicle->driver_id }}" @selected(old('driver_id', $vehicle->driver_id) == $vehicle->driver_id)>
                        {{ $vehicle->driver->name }} — {{ $vehicle->driver->phone }}</option>
                @endif
            </select>
        </div>
        @foreach (['vehicle_type' => 'Vehicle type', 'vehicle_number' => 'Vehicle number', 'vehicle_model' => 'Model', 'vehicle_color' => 'Color'] as $name => $label)
            <div class="field">
                <label for="{{ $name }}_{{ $formId }}">{{ __($label) }}</label>
                <div class="settings-input"><input id="{{ $name }}_{{ $formId }}"
                        name="{{ $name }}" value="{{ old($name, $vehicle->{$name}) }}" maxlength="255"
                        @required(in_array($name, ['vehicle_type', 'vehicle_number']))></div>
            </div>
        @endforeach
        <div class="field">
            <label for="status_{{ $formId }}">{{ __('Status') }}</label>
            <select id="status_{{ $formId }}" name="status" required>
                <option value="1" @selected(old('status', $vehicle->status ?? 1) == 1)>{{ __('Active') }}</option>
                <option value="0" @selected(old('status', $vehicle->status ?? 1) == 0)>{{ __('Inactive') }}</option>
            </select>
        </div>
    </div>
    <div class="vehicle-modal-actions">
        <button class="btn primary" type="submit" @disabled($drivers->isEmpty())>{{ __('Save vehicle') }}</button>
        <button class="btn" type="button" onclick="this.closest('dialog').close()">{{ __('Cancel') }}</button>
    </div>
</form>
