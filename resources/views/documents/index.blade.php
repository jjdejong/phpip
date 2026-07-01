@extends('layouts.app')

@section('content')
<div class="page-header">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="h4 mb-0">{{ __('Email Template Classes') }}</h1>
    <div class="page-actions">
      <a href="document/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Document class') }}" data-source="/document" data-resource="/document/create/">
        <svg width="14" height="14" fill="currentColor" class="me-1">
          <use xlink:href="#envelope-plus"/>
        </svg>
        {{ __('Create Email Template Class') }}
      </a>
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card border-primar p-1">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control" data-source="/document" name="Name" placeholder="{{ __('Name') }}"></th>
            <th><input class="form-control" data-source="/document" name="Notes" placeholder="{{ __('Notes') }}"></th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($template_classes as $class)
          <tr data-id="{{ $class->id }}" class="reveal-hidden">
            <td>
              <a href="/document/{{ $class->id }}" data-panel="ajaxPanel" title="{{ __('Class data') }}">
                {{ $class->name }}
              </a>
            </td>
            <td>{{ $class->notes }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $template_classes->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('Class information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on class to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
@endsection
