@extends('layouts.app')

@section('content')
<legend class="text-primary">
  {{ _i('Matter Types') }}
  <a href="type/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="{{ _i('Type') }}" data-resource="/type/">{{ _i('Create a new Matter Type') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/type" name="Code" placeholder="{{ _i('Code') }}"></th>
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/type" name="Type" placeholder="{{ _i('Type') }}"></th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($matter_types as $type)
          <tr class="reveal-hidden" data-id="{{ $type->code }}">
            <td>
              <a href="/type/{{ $type->code }}" data-panel="ajaxPanel" title="{{ _i('Type info') }}">
                {{ $type->code }}
              </a>
            </td>
            <td>{{ $type->type }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        type information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ _i('Click on type to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

@include('tables.table-js')

@endsection
