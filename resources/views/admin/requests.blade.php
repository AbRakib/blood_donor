@extends('admin.layouts.admin')

@section('page', 'requests')
@section('title', __('Blood requests'))

@section('hide-heading', true)

@section('content')
 <section class="admin-requests" id="requestsView"><div class="panel directory"><div class="dirheader"><div><h2>{{ __('Blood requests') }}</h2><p>{{ __('Manage blood requests, matching and donations') }}</p></div><button type="button" class="btn primary" onclick="openRequestForm()"><i class="fi fi-rr-plus" aria-hidden="true"></i> {{ __('Add request') }}</button></div><nav class="request-tabs" aria-label="{{ __('Blood requests') }}">
 @foreach (['all' => 'All', 'pending' => 'Pending', 'emergency' => 'Emergency', 'normal' => 'Normal', 'complete' => 'Complete', 'expired' => 'Expired'] as $tab => $label)
 <a href="{{ route('admin.blood-requests.index', ['tab' => $tab]) }}" @if (request('tab', 'all') === $tab) aria-current="page" @endif>{{ __($label) }}</a>
 @endforeach
 </nav><div class="tablewrap"><table><thead><tr><th scope="col">{{ __('Sl No.') }}</th><th scope="col">{{ __('Patient name') }}</th><th scope="col">{{ __('Group/Bags') }}</th><th scope="col">{{ __('Hospital') }}</th><th scope="col">{{ __('Status') }}</th><th scope="col">{{ __('Urgency') }}</th><th scope="col">{{ __('Contact') }}</th><th scope="col">{{ __('Actions') }}</th></tr></thead><tbody id="requestList"></tbody></table></div><div class="tablefooter"><span id="requestTableCount"></span><div class="pagination" id="requestPagination"></div></div></div></section>
@endsection
