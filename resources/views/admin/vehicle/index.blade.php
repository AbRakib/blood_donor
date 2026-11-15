@extends('admin.layouts.admin')
@section('page', 'vehicles')
@section('title', __('Vehicles'))
@section('actions')
    <button class="btn primary" type="button"
        onclick="document.getElementById('createVehicleModal').showModal()">{{ __('Add vehicle') }}</button>
@endsection
@section('content')
    @include('admin.vehicle._index')
    @include('admin.vehicle.modal.create')
    @include('admin.vehicle.modal.edit')
@endsection
