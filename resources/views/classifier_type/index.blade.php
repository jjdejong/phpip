@extends('layouts.app')

@section('content')
<div class="page-header">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="h4 mb-0">{{ __('Classifier Types') }}</h1>
    <div class="page-actions">
      <a href="classifier_type/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Type') }}" data-resource="/classifier_type/">
        <svg width="14" height="14" fill="currentColor" class="me-1">
          <use xlink:href="#plus-circle-fill"/>
        </svg>
        {{ __('Create Classifier Type') }}
      </a>
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th>
              <div class="input-group input-group-sm" style="width: 80px;">
                <input class="form-control" data-source="/classifier_type" name="Code" placeholder="{{ __('Code') }}">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="Code">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th>
              <div class="input-group input-group-sm" style="width: 150px;">
                <input class="form-control" data-source="/classifier_type" name="Type" placeholder="{{ __('Type') }}">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="Type">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th>{{ __('Category') }}</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($types as $type)
          <tr class="reveal-hidden" data-id="{{ $type->code }}">
            <td>
              <a href="/classifier_type/{{ $type->code }}" data-panel="ajaxPanel" title="{{ __('Type info') }}">
                {{ $type->code }}
              </a>
            </td>
            <td>{{ $type->type }}</td>
            <td>{{ is_null($type->category) ? '' :  $type->category->category }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('Type information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on type to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
@endsection
