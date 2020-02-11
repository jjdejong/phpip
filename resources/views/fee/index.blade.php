@extends('layouts.app')

@section('content')
<legend class="text-light">
  Fees
  <a href="fee/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="New lines" data-resource="/fee/">Add a new lines</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card overflow-auto" style="max-height: 640px;">
      <div class="card-header py-1">
        <div class="row font-weight-bold">
          <div class="row col-3">
            <div class="col-3">Country</div>
            <div class="col-3">Category</div>
            <div class="col-3">Origin</div>
            <div class="col-3">Qt</div>
          </div>
            <div class="col-1">Use after</div>
            <div class="col-1">Use before</div>
            <div class="col-3">Standard</div>
            <div class="col-3">Grace period</div>
            <div class="col-1">Currency</div>
          </div>
          <div  id="filter" class="row font-weight-bold" >
            <div class="row col-3">
              <div class="col-3"><input class="filter-input form-control form-control-sm" data-source="/country" name="Country" placeholder="Country" value="{{ old('Country') }}"></div>
              <div class="col-3"><input class="filter-input form-control form-control-sm" data-source="/category" name="Category" placeholder="Category" value="{{ old('Category') }}"></div>
              <div class="col-3"><input class="filter-input form-control form-control-sm" data-source="/country" name="Origin" placeholder="Origin" value="{{ old('Origin') }}"></div>
              <div class="col-3"><input class="filter-input form-control form-control-sm" name="Qt" placeholder="Qt" value="{{ old('Qt') }}"></div>
            </div>
            <div class="col-2"></div>
            <div class="row col-3">
              <div class="col-6">Standard</div>
              <div class="col-6">Reduced</div>
            </div>
            <div class="row col-3">
              <div class="col-6">Standard</div>
              <div class="col-6">Reduced</div>
            </div>
            <div class="col-1"></div>
          </div>
          <div class="row font-weight-bold" >
            <div class="col-5"></div>
            <div class="row col-3">
              <div class="col-3">Fee</div>
              <div class="col-3">Cost</div>
              <div class="col-3">Fee</div>
              <div class="col-3">Cost</div>
            </div>
            <div class="row col-3">
              <div class="col-3">Fee</div>
              <div class="col-3">Cost</div>
              <div class="col-3">Fee</div>
              <div class="col-3">Cost</div>
            </div>
            <div class="col-1"></div>
          </div>
        </div>
        <div class="card-body pt-2" id="tableList">
        @foreach ($fees as $fee)
          <div class="row font-weight-bold" data-resource="/fee/{{ $fee->id }}">
            <div class="row col-3">
              <div class="col-3">{{ $fee->for_country }}</div>
              <div class="col-3">{{ $fee->for_category }}</div>
              <div class="col-3">{{ $fee->for_origin }}</div>
              <div class="col-3">{{ $fee->qt }}</div>
            </div>
            <div class="col-1">{{ $fee->use_after }}</div>
            <div class="col-1">{{ $fee->use_before }}</div>
            <div class="row col-3">
              <div class="col-3"><input type="text" class="form-control noformat" name="fee" value="{{ $fee->fee }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="cost" value="{{ $fee->cost }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="fee_reduced" value="{{ $fee->fee_reduced }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="cost_reduced" value="{{ $fee->cost_reduced }}"></div>
            </div>
            <div class="row col-3">
              <div class="col-3"><input type="text" class="form-control noformat" name="fee_sup" value="{{ $fee->fee_sup }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="cost_sup" value="{{ $fee->cost_sup }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="fee_sup_reduced" value="{{ $fee->fee_sup_reduced }}"></div>
              <div class="col-3"><input type="text" class="form-control noformat" name="cost_sup_reduced" value="{{ $fee->cost_sup_reduced }}"></div>
            </div>
            <div class="col-1"><input type="text" class="form-control noformat" name="currency" value="{{ $fee->currency }}"></div>
          </div>
        @endforeach
        </div>
    </div>
  </div>
</div>

@endsection

@section('script')

@include('tables.table-js')

@stop
