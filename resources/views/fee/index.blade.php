@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <legend class="text-light">
    Fees
    <a href="fee/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="New line" data-resource="/fee/">Add a new line</a>
  </legend>
  <div class="card overflow-auto" style="max-height: 640px;">
    <div class="card-header py-1">
      <div class="row font-weight-bold text-center">
        <div class="row col-3"></div>
        <div class="col-4 border">Standard</div>
        <div class="col-4 border">Grace period</div>
        <div class="col-1"></div>
      </div>
      <div id="filter" class="row font-weight-bold text-center">
        <div class="row col-3">
          <div class="col-3 px-0"><input class="filter-input form-control form-control-sm" data-source="/country" name="Country" placeholder="Country" value="{{ old('Country') }}"></div>
          <div class="col-3 px-0"><input class="filter-input form-control form-control-sm" data-source="/category" name="Category" placeholder="Category" value="{{ old('Category') }}"></div>
          <div class="col-3 px-0"><input class="filter-input form-control form-control-sm" data-source="/country" name="Origin" placeholder="Origin" value="{{ old('Origin') }}"></div>
          <div class="col-3 px-0"><input class="filter-input form-control form-control-sm" name="Qt" placeholder="Qt" value="{{ old('Qt') }}"></div>
        </div>
        <div class="row col-4">
          <div class="col-6 border">Standard</div>
          <div class="col-6 border">Reduced</div>
        </div>
        <div class="row col-4">
          <div class="col-6 border">Standard</div>
          <div class="col-6 border">Reduced</div>
        </div>
        <div class="col-1"></div>
      </div>
      <div class="row text-center" >
        <div class="col-3"></div>
        <div class="row col-4">
          <div class="col-3">Cost</div>
          <div class="col-3">Fee</div>
          <div class="col-3">Cost</div>
          <div class="col-3">Fee</div>
        </div>
        <div class="row col-4">
          <div class="col-3">Cost</div>
          <div class="col-3">Fee</div>
          <div class="col-3">Cost</div>
          <div class="col-3">Fee</div>
        </div>
        <div class="col-1">Currency</div>
        {{-- <div class="col-1">Use after</div>
        <div class="col-1">Use before</div> --}}
      </div>
    </div>
    <div class="card-body pt-2" id="tableList">
    @foreach ($fees as $fee)
      <div class="row" data-resource="/fee/{{ $fee->id }}">
        <div class="row col-3">
          <div class="col-3">{{ $fee->for_country }}</div>
          <div class="col-3">{{ $fee->for_category }}</div>
          <div class="col-3">{{ $fee->for_origin }}</div>
          <div class="col-3">{{ $fee->qt }}</div>
        </div>
        <div class="row col-4">
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="cost" value="{{ $fee->cost }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="fee" value="{{ $fee->fee }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="cost_reduced" value="{{ $fee->cost_reduced }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="fee_reduced" value="{{ $fee->fee_reduced }}"></div>
        </div>
        <div class="row col-4">
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="cost_sup" value="{{ $fee->cost_sup }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="fee_sup" value="{{ $fee->fee_sup }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="cost_sup_reduced" value="{{ $fee->cost_sup_reduced }}"></div>
          <div class="col-3"><input type="text" class="form-control noformat text-right" name="fee_sup_reduced" value="{{ $fee->fee_sup_reduced }}"></div>
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

@stop
