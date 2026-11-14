@extends('admin.layouts.admin')
@section('page', 'drivers')
@section('title', __('Drivers'))
@section('hide-heading', true)
@section('content')
@if(session('success'))
    <p role="status">{{ session('success') }}</p>
@endif
<section class="admin-drivers" id="driversView">
    <div class="panel directory">
        <div class="dirheader">
            <div>
                <h2>{{ __('Drivers') }}</h2>
                <p>{{ __('Manage driver contact details and driving credentials.') }}</p>
            </div>
            <a class="btn primary" href="{{ route('admin.drivers.create') }}"><i class="fi fi-rr-plus" aria-hidden="true"></i> {{ __('Add driver') }}</a>
        </div>
        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Sl No.') }}</th>
                        <th scope="col">{{ __('Driver name') }}</th>
                        <th scope="col">{{ __('Phone') }}</th>
                        <th scope="col">{{ __('Email') }}</th>
                        <th scope="col">{{ __('License number') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                        <tr>
                            <td>{{ $drivers->firstItem() + $loop->index }}.</td>
                            <td>
                                <div class="driver-name">
                                    <span class="driver-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::substr($driver->name, 0, 1) }}</span>
                                    <strong>{{ $driver->name }}</strong>
                                </div>
                            </td>
                            <td><a class="driver-phone" href="tel:{{ $driver->phone }}">{{ $driver->phone }}</a></td>
                            <td>{{ $driver->email ?? '—' }}</td>
                            <td>{{ $driver->license_number ?? '—' }}</td>
                            <td><span @class(['driver-status', 'is-active' => $driver->status, 'is-inactive' => ! $driver->status])>{{ $driver->status ? __('Active') : __('Inactive') }}</span></td>
                            <td>
                                <details class="driver-row-menu row-menu">
                                    <summary aria-label="{{ __('Actions for :name', ['name' => $driver->name]) }}"><i class="fi fi-rr-angle-small-down" aria-hidden="true"></i></summary>
                                    <div>
                                        <a class="row-btn" href="{{ route('admin.drivers.show', $driver) }}">{{ __('View') }}</a>
                                        <a class="row-btn" href="{{ route('admin.drivers.edit', $driver) }}">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('admin.drivers.destroy', $driver) }}" onsubmit="return confirm(@js(__('Delete this driver?')))" >
                                            @csrf
                                            @method('DELETE')
                                            <button class="row-btn" type="submit">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="7">{{ __('No drivers found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="tablefooter">
            <span>{{ __('Page :current of :last', ['current' => $drivers->currentPage(), 'last' => $drivers->lastPage()]) }}</span>
            <div class="pagination">
                @if($drivers->previousPageUrl())<a class="btn" href="{{ $drivers->previousPageUrl() }}" aria-label="{{ __('Previous page') }}">{{ __('Previous') }}</a>@endif
                @if($drivers->nextPageUrl())<a class="btn" href="{{ $drivers->nextPageUrl() }}" aria-label="{{ __('Next page') }}">{{ __('Next') }}</a>@endif
            </div>
        </div>
    </div>
</section>
@endsection
