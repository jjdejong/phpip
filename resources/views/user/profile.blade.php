@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  My Profile
</legend>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Make sure we get the fresh user data --}}
@php
  $userInfo = Auth::user()->fresh();
@endphp

@include('user.show', ['isProfileView' => true, 'userInfo' => $userInfo])
@endsection