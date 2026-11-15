@extends('admin.layouts.admin')

@section('page', 'requests')
@section('title', __('Blood requests'))
@section('hide-heading', true)

@section('content')
    @include('admin.blood_request._index')
    @include('admin.blood_request.modal.create')
    @include('admin.blood_request.modal.edit')
@endsection
