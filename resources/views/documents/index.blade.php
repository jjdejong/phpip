@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  Email Template Classes
  <a href="document/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="Document class" data-source="/document" data-resource="/document/create/">Create Email Template Class</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primar p-1">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control" data-source="/document" name="Name" placeholder="Name"></th>
            <th><input class="form-control" data-source="/document" name="Notes" placeholder="Notes"></th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($template_classes as $class)
          <tr data-id="{{ $class->id }}" class="reveal-hidden">
            <td>
              <a href="/document/{{ $class->id }}" data-panel="ajaxPanel" title="{{ _i('Class data') }}">
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
        {{ _i("Class information") }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ _i("Click on class to view and edit details") }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
