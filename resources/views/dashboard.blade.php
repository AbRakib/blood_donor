@extends('layouts.admin')

@section('page', 'dashboard')
@section('title', __('Dashboard'))
@section('subtitle', __('View all activities of Manda Blood Donation Program at a glance'))

@section('content')
@include('partials.admin-overview')
@include('partials.admin-donors')
@endsection
