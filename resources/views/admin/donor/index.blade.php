@extends('admin.layouts.admin')

@section('page', 'donors')
@section('title', __('Blood donors'))
@section('hide-heading', true)

@section('content')
    @include('admin.donor._index')
    @include('admin.donor.modal.edit')
@endsection
