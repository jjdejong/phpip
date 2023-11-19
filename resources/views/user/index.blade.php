@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2">
    {{ __('Users') }}
    <a href="user/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Create User') }}">{{ __('Create user') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1">
      <table class="table table-striped table-hover table-sm">
        <thead class="card-header">
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control" name="Name" placeholder="{{ __('Name') }}" value="{{ Request::get('Name') }}"></th>
            <th>{{ __('Role') }}</th>
            <th>{{ __('Login') }}</th>
            <th>{{ __('Company') }}</th>
          </tr>
        </thead>
        <tbody id="tableList" class="card-body">
          @foreach ($userslist as $user)
          <tr class="reveal-hidden" data-id="{{ $user->id }}">
            <td>
              <a @if($user->warn) class="text-danger text-decoration-none" @endif href="/user/{{ $user->id }}" data-panel="ajaxPanel" title="{{ __('User data') }}">
                {{ $user->name }}
              </a>
            </td>
            <td>{{ $user->default_role }}</td>
            <td>{{ $user->login }}</td>
            <td>{{ empty($user->company) ? '' : $user->company->name }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $userslist->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('User information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on user name to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
