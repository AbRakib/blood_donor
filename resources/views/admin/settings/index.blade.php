@extends('admin.layouts.admin')

@section('page', 'settings')
@section('title', __('Settings'))
@section('hide-heading', 'true')

@section('content')
    @include('admin.settings._index')
    @include('admin.settings.modal.create')
    @include('admin.settings.modal.edit')
@endsection
