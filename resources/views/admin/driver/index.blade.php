@extends('admin.layouts.admin')
@section('page', 'drivers')
@section('title', __('Drivers'))
@section('hide-heading', true)
@section('content')
    @include('admin.driver._index')
@endsection
