@extends('layouts.app')

@section('content')
<legend class="text-primary">
  Categories
  <a href="category/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Category" data-resource="/category/">Create a new Category</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card overflow-auto border-primary" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/category" name="Code" placeholder="Code""></th>
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/category" name="Category" placeholder="Category"></th>
            <th class="align-middle border-top-0" colspan="2">Display with</th>
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
