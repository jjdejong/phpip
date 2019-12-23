@extends('layouts.app')

@section('content')
<legend class="text-light">
  Default actors
  <a href="dactor/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Default actors" data-resource="/dactor/">Add a new default actor</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <td><input class="filter-input form-control form-control-sm" data-source="/dactor" name="Actor" placeholder="Actor" value="{{ old('Actor') }}"></td>
            <td><input class="filter-input form-control form-control-sm" data-source="/dactor" name="Role" placeholder="Role" value="{{ old('Role') }}"></td>
            <td><input class="filter-input form-control form-control-sm" data-source="/dactor" name="Country" placeholder="Country" value="{{ old('Country') }}"></td>
            <td><input class="filter-input form-control form-control-sm" data-source="/dactor" name="Category" placeholder="Category" value="{{ old('Category') }}"></td>
            <td><input class="filter-input form-control form-control-sm" data-source="/dactor" name="Client" placeholder="Client" value="{{ old('Client') }}"></td>
          </tr>
        </thead>
        <tbody id="ruleList">
          @foreach ($dactors as $dactor)
          <tr class="reveal-hidden" data-id="{{ $dactor->id }}">
            <td>
              <a href="/dactor/{{ $dactor->id }}" data-panel="ajaxPanel" title="Actor">
                {{ $dactor->actor->name }}
              </a>
            </td>
            <td>{{ empty($dactor->roleInfo) ? '' : $dactor->roleInfo->name }}</td>
            <td>{{ empty($dactor->country) ? '' : $dactor->country->name }}</td>
            <td>{{ empty($dactor->category) ? '' : $dactor->category->category }}</td>
            <td>{{ empty($dactor->client) ? '' : $dactor->client->name }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        Default actor information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on line to view and edit details
        </div>
      </div>
    </div>
    <span id="footerAlert" class="alert float-left"></span>
  </div>
</div>

@endsection

@section('script')

@include('tables.table-js')

@stop
