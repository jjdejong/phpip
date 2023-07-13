@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  Categories
  <a href="category/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="Category" data-resource="/category/">Create Category</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control" data-source="/category" name="Code" placeholder="Code""></th>
            <th><input class="form-control" data-source="/category" name="Category" placeholder="Category"></th>
            <th colspan="2">Display with</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($categories as $category)
          <tr class="reveal-hidden" data-id="{{ $category->code }}">
            <td>
              <a href="/category/{{ $category->code }}" data-panel="ajaxPanel" title="Category info">
                {{ $category->code }}
              </a>
            </td>
            <td>{{ $category->category }}</td>
            <td>{{ $category->displayWithInfo->category }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        Category information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on category to view and edit details
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
