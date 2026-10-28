@extends('layouts.admin')

@section('page', 'groups')
@section('title', __('Blood group'))

@section('content')
<section class="panel directory" id="groupsView"><div class="dirheader"><h2>{{ __('Blood group') }}</h2></div><div class="tablewrap"><table><thead><tr><th scope="col">{{ __('Sl No.') }}</th><th scope="col">{{ __('Blood group') }}</th><th scope="col">{{ __('Number of donors') }}</th><th scope="col">{{ __('Created date') }}</th><th scope="col">{{ __('Created by') }}</th></tr></thead><tbody id="groupsRows"></tbody></table></div></section>
@endsection
