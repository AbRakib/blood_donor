@extends('layouts.admin')

@section('page', 'contacts')
@section('title', __('Contact requests'))

@section('content')
<section class="panel directory" id="contactsView"><div class="dirheader"><h2>{{ __('Contact requests') }}</h2></div><div class="tablewrap"><table><thead><tr><th scope="col">{{ __('Sl No.') }}</th><th scope="col">{{ __('Contact name') }}</th><th scope="col">{{ __('Contact phone') }}</th><th scope="col">{{ __('Donor name') }}</th><th scope="col">{{ __('Message') }}</th><th scope="col">{{ __('Status') }}</th><th scope="col">{{ __('Actions') }}</th></tr></thead><tbody id="contactsRows"></tbody></table></div><div class="tablefooter"><span id="contactsTableCount"></span><div class="pagination" id="contactsPagination"></div></div></section>
@endsection
