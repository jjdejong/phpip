@extends('layouts.app')

@section('content')
<legend class="text-primary">
  Classifier Types
  <a href="classifier_type/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Type" data-resource="/classifier_type/">Create a new Classifier Type</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/classifier_type" name="Code" placeholder="Code"></th>
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/classifier_type" name="Type" placeholder="Type"></th>
            <th class="align-middle border-top-0">Category</th>
          </tr>
        </thead>
        <tbody id="tableList">
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
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}"></script>
@endsection
