@extends('layouts.app')

@section('content')
<div class="page-header">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="h4 mb-0">{{ __('My Profile') }}</h1>
  </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
</div>
@endif

{{-- Make sure we get the fresh user data --}}
@php
  $userInfo = Auth::user()->fresh();
@endphp

@include('user.show', ['isProfileView' => true, 'userInfo' => $userInfo])
@endsection