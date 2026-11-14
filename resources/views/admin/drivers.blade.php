@extends('admin.layouts.admin')
@section('page', 'drivers')
@section('title', __('Drivers'))
@section('actions')
<a class="btn primary" href="{{ route('admin.drivers.create') }}">{{ __('Add driver') }}</a>
@endsection
@section('content')
@if(session('success'))
    <p role="status">{{ session('success') }}</p>
@endif
<section class="panel directory" id="driversView">
    <div class="tablewrap">
        <table>
            <thead><tr>
                @foreach(['Name', 'Phone', 'Email', 'License number', 'Status', 'Actions'] as $label)
                    <th scope="col">{{ __($label) }}</th>
                @endforeach
            </tr></thead>
            <tbody>
                @forelse($drivers as $driver)
                    <tr>
                        <td>{{ $driver->name }}</td><td>{{ $driver->phone }}</td>
                        <td>{{ $driver->email ?? '—' }}</td><td>{{ $driver->license_number ?? '—' }}</td>
                        <td>{{ $driver->status ? __('Active') : __('Inactive') }}</td>
                        <td>
                            <a class="btn" href="{{ route('admin.drivers.show', $driver) }}">{{ __('View') }}</a>
                            <a class="btn" href="{{ route('admin.drivers.edit', $driver) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.drivers.destroy', $driver) }}" onsubmit="return confirm(@js(__('Delete this driver?')))" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn" type="submit">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">{{ __('No drivers found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="dirheader">
        @if($drivers->previousPageUrl())<a class="btn" href="{{ $drivers->previousPageUrl() }}">{{ __('Previous') }}</a>@endif
        <span>{{ $drivers->currentPage() }} / {{ $drivers->lastPage() }}</span>
        @if($drivers->nextPageUrl())<a class="btn" href="{{ $drivers->nextPageUrl() }}">{{ __('Next') }}</a>@endif
    </div>
</section>
@endsection
