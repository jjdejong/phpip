@extends('layouts.app')

@section('content')
<legend>Categories
  <a href="category/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Category" data-resource="/category/">Create a new Category</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Display with</th>
            <th>&nbsp;</th>
          </tr>
          <tr id="filter">
            <th><input class="filter-input form-control form-control-sm" data-source="/category" name="Code" placeholder="Code" value="{{ old('Code') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/category" name="Category" placeholder="Category" value="{{ old('Category') }}"></th>
            <th colspan="2"></th>
          </tr>
        </thead>
        <tbody id="ruleList">
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
      <div class="card-header bg-info">
        Category information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on category to view and edit details
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
