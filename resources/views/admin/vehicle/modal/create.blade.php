    <dialog class="vehicle-modal" id="createVehicleModal" aria-labelledby="createVehicleTitle"
        @if ($errors->any() && !old('_method')) open @endif>
        <div class="modal-header">
            <h2 id="createVehicleTitle">{{ __('Add vehicle') }}</h2>
            <form method="dialog"><button class="close" aria-label="{{ __('Close') }}">×</button></form>
        </div>
        @include('admin.vehicle.modal._edit', [
            'vehicle' => new \App\Models\Vehicle(),
            'formId' => 'createVehicleForm',
        ])
    </dialog>
