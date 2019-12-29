@extends('layouts.app')

@section('content')
<legend class="text-light">
  Classifier types
  <a href="classifier_type/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Type" data-resource="/classifier_type/">Create a new Classifier Type</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <td><input class="filter-input form-control form-control-sm" data-source="/classifier_type" name="Code" placeholder="Code" value="{{ old('Code') }}"></td>
            <td><input class="filter-input form-control form-control-sm" data-source="/classifier_type" name="Type" placeholder="Type" value="{{ old('Type') }}"></td>
            <td>Category</th>
          </tr>
        </thead>
        <tbody id="ruleList">
          @foreach ($types as $type)
          <tr class="reveal-hidden" data-id="{{ $type->code }}">
            <td>
              <a href="/classifier_type/{{ $type->code }}" data-panel="ajaxPanel" title="Type info">
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
        Type information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on type to view and edit details
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
