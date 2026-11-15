    @foreach ($vehicles as $vehicle)
        <dialog class="vehicle-modal" id="editVehicleModal{{ $vehicle->id }}"
            aria-labelledby="editVehicleTitle{{ $vehicle->id }}" @if ($errors->any() && (string) old('_method') === 'PUT' && (string) old('vehicle_id') === (string) $vehicle->id) open @endif>
            <div class="modal-header">
                <h2 id="editVehicleTitle{{ $vehicle->id }}">{{ __('Edit vehicle') }}</h2>
                <form method="dialog"><button class="close" aria-label="{{ __('Close') }}">×</button></form>
            </div>
            @include('admin.vehicle.modal._edit', [
                'vehicle' => $vehicle,
                'formId' => 'editVehicleForm' . $vehicle->id,
            ])
        </dialog>
    @endforeach
