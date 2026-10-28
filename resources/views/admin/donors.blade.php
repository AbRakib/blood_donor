@extends('layouts.admin')

@section('page', 'donors')
@section('title', __('Blood donors'))

@section('hide-heading', true)

@section('content')
@include('partials.admin-donors')
@endsection
