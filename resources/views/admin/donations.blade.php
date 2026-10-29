@extends('admin.layouts.admin')

@section('page', 'donations')
@section('title', __('Donation history'))

@section('hide-heading', true)

@section('content')
<section class="panel directory" id="donationsView"><div class="dirheader"><h2>{{ __('Donation history') }}</h2><button class="btn primary" data-action="new-donation"><i class="fi fi-rr-plus" aria-hidden="true"></i> {{ __('Record donation') }}</button></div><div class="tablewrap"><table><thead><tr><th scope="col">{{ __('Sl No.') }}</th><th scope="col">{{ __('Donor name') }}</th><th scope="col">{{ __('Donation date') }}</th><th scope="col">{{ __('Hospital') }}</th><th scope="col">{{ __('Number of bags') }}</th><th scope="col">{{ __('Status') }}</th><th scope="col">{{ __('Actions') }}</th></tr></thead><tbody id="donationsRows"></tbody></table></div><div class="tablefooter"><span id="donationsTableCount"></span><div class="pagination" id="donationsPagination"></div></div></section>
@endsection
