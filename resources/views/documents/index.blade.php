@extends('layouts.app')

@section('content')
<legend class="text-light">
  Documents classes
  <a href="document/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Document class" data-source="/document" data-resource="/document/create/">Create a new class of templates</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th><input class="filter-input form-control form-control-sm" data-source="/document" name="Name" placeholder="Name" value="{{ old('Name') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/document" name="Description" placeholder="Description" value="{{ old('Description') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/document" value="{{ old('Category') }}" name="Category" placeholder="Category" /></th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($template_classes as $class)
          <tr data-id="{{ $class->id }}" class="reveal-hidden">
            <td>
              <a href="/document/{{ $class->id }}" data-panel="ajaxPanel" title="Class data">
                {{ $class->name }}
              </a>
            </td>
            <td>{{ $class->description }}</td>
            <td>{{ empty($class->category) ? '' : $class->category->category }}</td>
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
        Class information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on class to view and edit details
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')

@include('tables.table-js')

@stop
