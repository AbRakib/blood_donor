@extends('admin.layouts.admin')
@section('page', 'vehicles')
@section('title', __('Vehicles'))
@section('actions')
<a class="btn primary" href="{{ route('admin.vehicles.create') }}">{{ __('Add vehicle') }}</a>
@endsection
@section('content')
@if(session('success'))
    <p role="status">{{ session('success') }}</p>
@endif
<section class="panel directory" id="vehiclesView">
    <div class="tablewrap">
        <table>
            <thead><tr>
                @foreach(['Vehicle number', 'Type', 'Model', 'Color', 'Driver', 'Status', 'Actions'] as $label)
                    <th scope="col">{{ __($label) }}</th>
                @endforeach
            </tr></thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->vehicle_number }}</td><td>{{ $vehicle->vehicle_type }}</td>
                        <td>{{ $vehicle->vehicle_model ?? '—' }}</td><td>{{ $vehicle->vehicle_color ?? '—' }}</td>
                        <td>{{ $vehicle->driver?->name ?? '—' }}</td><td>{{ $vehicle->status ? __('Active') : __('Inactive') }}</td>
                        <td>
                            <a class="btn" href="{{ route('admin.vehicles.show', $vehicle) }}">{{ __('View') }}</a>
                            <a class="btn" href="{{ route('admin.vehicles.edit', $vehicle) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" onsubmit="return confirm(@js(__('Delete this vehicle?')))" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn" type="submit">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">{{ __('No vehicles found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="dirheader">
        @if($vehicles->previousPageUrl())<a class="btn" href="{{ $vehicles->previousPageUrl() }}">{{ __('Previous') }}</a>@endif
        <span>{{ $vehicles->currentPage() }} / {{ $vehicles->lastPage() }}</span>
        @if($vehicles->nextPageUrl())<a class="btn" href="{{ $vehicles->nextPageUrl() }}">{{ __('Next') }}</a>@endif
    </div>
</section>
@endsection
