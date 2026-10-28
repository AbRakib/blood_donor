@extends('layouts.admin')

@section('page', 'reports')
@section('title', __('Reports'))

@section('actions')
<div class="heading-actions"><button class="btn" onclick="exportCSV()"><i class="fi fi-rr-download" aria-hidden="true"></i> {{ __('Export') }}</button></div>
@endsection

@section('content')
@include('partials.admin-overview')
@endsection
