@extends('admin.layouts.admin')
@section('page', 'vehicles')
@section('title', __('Vehicles'))
@section('actions')
    <button class="btn primary" type="button"
        onclick="document.getElementById('createVehicleModal').showModal()">{{ __('Add vehicle') }}</button>
@endsection
@section('content')
    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif
    <section class="panel directory" id="vehiclesView">
        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        @foreach (['Vehicle number', 'Type', 'Model', 'Color', 'Driver', 'Status', 'Actions'] as $label)
                            <th scope="col">{{ __($label) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->vehicle_number }}</td>
                            <td>{{ $vehicle->vehicle_type }}</td>
                            <td>{{ $vehicle->vehicle_model ?? '—' }}</td>
                            <td>{{ $vehicle->vehicle_color ?? '—' }}</td>
                            <td>{{ $vehicle->driver?->name ?? '—' }}</td>
                            <td>{{ $vehicle->status ? __('Active') : __('Inactive') }}</td>
                            <td>
                                <a class="btn" href="{{ route('admin.vehicles.show', $vehicle) }}">{{ __('View') }}</a>
                                <button class="btn" type="button"
                                    onclick="document.getElementById('editVehicleModal{{ $vehicle->id }}').showModal()">{{ __('Edit') }}</button>
                                <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                                    onsubmit="return confirm(@js(__('Delete this vehicle?')))" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn" type="submit">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <center>
                                    {{ __('No vehicles found.') }}
                                </center>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="dirheader">
            @if ($vehicles->previousPageUrl())
                <a class="btn" href="{{ $vehicles->previousPageUrl() }}">{{ __('Previous') }}</a>
            @endif
            <span>{{ $vehicles->currentPage() }} / {{ $vehicles->lastPage() }}</span>
            @if ($vehicles->nextPageUrl())
                <a class="btn" href="{{ $vehicles->nextPageUrl() }}">{{ __('Next') }}</a>
            @endif
        </div>
    </section>

    <dialog class="vehicle-modal" id="createVehicleModal" aria-labelledby="createVehicleTitle"
        @if ($errors->any() && !old('_method')) open @endif>
        <div class="modal-header">
            <h2 id="createVehicleTitle">{{ __('Add vehicle') }}</h2>
            <form method="dialog"><button class="close" aria-label="{{ __('Close') }}">×</button></form>
        </div>
        @include('admin.partials.vehicle-form-fields', [
            'vehicle' => new \App\Models\Vehicle(),
            'formId' => 'createVehicleForm',
        ])
    </dialog>

    @foreach ($vehicles as $vehicle)
        <dialog class="vehicle-modal" id="editVehicleModal{{ $vehicle->id }}"
            aria-labelledby="editVehicleTitle{{ $vehicle->id }}" @if ($errors->any() && (string) old('_method') === 'PUT' && (string) old('vehicle_id') === (string) $vehicle->id) open @endif>
            <div class="modal-header">
                <h2 id="editVehicleTitle{{ $vehicle->id }}">{{ __('Edit vehicle') }}</h2>
                <form method="dialog"><button class="close" aria-label="{{ __('Close') }}">×</button></form>
            </div>
            @include('admin.partials.vehicle-form-fields', [
                'vehicle' => $vehicle,
                'formId' => 'editVehicleForm' . $vehicle->id,
            ])
        </dialog>
    @endforeach

    <style>
        .vehicle-modal {
            position: fixed;
            inset: 0;
            margin: auto;
            width: min(620px, calc(100% - 32px));
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 24px 80px #0003;
        }

        .vehicle-modal[open] {
            display: block;
        }

        .vehicle-modal::backdrop {
            background: #1e293b78;
        }

        .vehicle-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .vehicle-modal .modal-header h2 {
            margin: 0;
            font-size: 22px;
        }

        .vehicle-modal .settings-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .vehicle-modal .field {
            min-width: 0;
        }

        .vehicle-modal .field select,
        .vehicle-modal .field input {
            width: 100%;
        }

        .vehicle-modal .vehicle-modal-actions {
            display: flex;
            gap: 10px;
        }

        @media (max-width: 560px) {
            .vehicle-modal .settings-fields {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.querySelectorAll('.vehicle-modal').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) modal.close();
            });
        });
    </script>
@endsection
