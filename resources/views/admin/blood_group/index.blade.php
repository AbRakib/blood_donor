@extends('admin.layouts.admin')

@section('page', 'groups')
@section('title', __('Blood group'))
@section('hide-heading', true)

@section('content')
    @include('admin.blood_group._index')
    @include('admin.blood_group.modal.create')
    @include('admin.blood_group.modal.edit')
@endsection
