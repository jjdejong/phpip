@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <legend class="text-primary">
    Fees
    <a href="fee/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="{{ _i('New line') }}" data-resource="/fee/">{{ _i('Add a new line') }}</a>
  </legend>
  <div class="card border-primary overflow-auto" style="max-height: 640px;">
    <div class="card-header bg-primary text-light py-1">
      <div class="row text-center">
        <div class="col-3"></div>
        <div class="col-4 py-2">{{ _i("Normal") }}</div>
        <div class="col-4 bg-info py-2">{{ _i("Grace period") }}</div>
        <div class="col-1"></div>
      </div>
      <div id="filter" class="row text-center">
        <div class="col-3">
          <div class="row">
            <div class="col-3 pl-0"><input class="filter-input form-control form-control-sm" data-source="/country" name="{{ _i('Country') }}" placeholder="{{ _i('Country') }}"></div>
            <div class="col-3 pl-0"><input class="filter-input form-control form-control-sm" data-source="/category" name="{{ _i('Category') }}" placeholder="{{ _i('Category') }}"></div>
            <div class="col-3 pl-0"><input class="filter-input form-control form-control-sm" data-source="/country" name="{{ _i('Origin') }}" placeholder="{{ _i('Origin') }}"></div>
            <div class="col-3 pl-0"><input class="filter-input form-control form-control-sm" name="{{ _i('Qt') }}" placeholder="{{ _i('Yr') }}"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col-6 py-2">{{ _i('Standard') }}</div>
            <div class="bg-secondary col-6 py-2">{{ _i('Reduced') }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col-6 py-2">{{ _i('Standard') }}</div>
            <div class="bg-secondary col-6 py-2">{{ _i('Reduced') }}</div>
          </div>
        </div>
        <div class="col-1"></div>
      </div>
      <div class="row text-center">
        <div class="col-3"></div>
        <div class="col-4">
          <div class="row">
            <div class="col-3 py-1">{{ _i('Cost') }}</div>
            <div class="col-3 py-1">{{ _i('Fee') }}</div>
            <div class="bg-secondary col-3 py-1">{{ _i('Cost') }}</div>
            <div class="bg-secondary col-3 py-1">{{ _i("Fee") }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col-3 py-1">{{ _i("Cost") }}</div>
            <div class="bg-info col-3 py-1">{{ _i("Fee") }}</div>
            <div class="bg-secondary col-3 py-1">{{ _i("Cost") }}</div>
            <div class="bg-secondary col-3 py-1">{{ _i("Fee") }}</div>
          </div>
        </div>
        <div class="col-1">{{ _i("Currency") }}</div>
        {{-- <div class="col-1">{{ _i("Use after") }}</div>
        <div class="col-1">{{ _i("Use before") }}</div> --}}
      </div>
    </div>
    <div class="card-body pt-2" id="tableList">
    @foreach ($fees as $fee)
      <div class="row" data-resource="/fee/{{ $fee->id }}">
        <div class="col-3">
          <div class="row">
            <div class="col-3">{{ $fee->for_country }}</div>
            <div class="col-3">{{ $fee->for_category }}</div>
            <div class="col-3">{{ $fee->for_origin }}</div>
            <div class="col-3">{{ $fee->qt }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col-3 bg-light pr-3"><input type="text" class="form-control noformat text-right" name="cost" value="{{ $fee->cost }}"></div>
            <div class="col-3 bg-light pr-3"><input type="text" class="form-control noformat text-right" name="fee" value="{{ $fee->fee }}"></div>
            <div class="col-3 pr-3"><input type="text" class="form-control noformat text-right" name="cost_reduced" value="{{ $fee->cost_reduced }}"></div>
            <div class="col-3 pr-3"><input type="text" class="form-control noformat text-right" name="fee_reduced" value="{{ $fee->fee_reduced }}"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col-3 bg-light pr-3"><input type="text" class="form-control noformat text-right" name="cost_sup" value="{{ $fee->cost_sup }}"></div>
            <div class="col-3 bg-light pr-3"><input type="text" class="form-control noformat text-right" name="fee_sup" value="{{ $fee->fee_sup }}"></div>
            <div class="col-3 pr-3"><input type="text" class="form-control noformat text-right" name="cost_sup_reduced" value="{{ $fee->cost_sup_reduced }}"></div>
            <div class="col-3 pr-3"><input type="text" class="form-control noformat text-right" name="fee_sup_reduced" value="{{ $fee->fee_sup_reduced }}"></div>
          </div>
        </div>
        <div class="col-1"><input type="text" class="form-control noformat text-center" name="currency" value="{{ $fee->currency }}"></div>
        {{-- <div class="col-1">{{ $fee->use_after }}</div>
        <div class="col-1">{{ $fee->use_before }}</div> --}}
      </div>
    @endforeach
    </div>
  </div>
</div>

@endsection

@section('script')

@include('tables.table-js')

@endsection
