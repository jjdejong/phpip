@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  {{ __('Actor Roles') }}
  <a href="role/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Role') }}" data-resource="/role/">{{ __('Create Role') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1" style="max-height: 640px; overflow: auto;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th>
              <div class="input-group input-group-sm" style="width: 80px;">
                <input class="form-control" data-source="/role" name="Code" placeholder="{{ __('Code') }}">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="Code">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th>
              <div class="input-group input-group-sm" style="width: 150px;">
                <input class="form-control" data-source="/role" name="Name" placeholder="{{ __('Name') }}">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="Name">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th class="text-center" colspan="2">{{ __('Notes') }}</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($roles as $role)
          <tr class="reveal-hidden" data-id="{{ $role->code }}">
            <td>
              <a href="/role/{{ $role->code }}" data-panel="ajaxPanel" title="{{ __('Role info') }}">
                {{ $role->code }}
              </a>
            </td>
            <td>{{ $role->name }}</td>
            <td>{{ $role->notes }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('Role information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on role to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
