@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    Fees
    <a href="fee/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="New line" data-resource="/fee/">Add a new line</a>
  </legend>
  <div class="card border-primary">
    <div class="card-header bg-primary text-light py-1">
      <div class="row text-center">
        <div class="col-3 align-bottom"></div>
        <div class="col-4">
          <div class="row">
            <div class="col-6 py-2">Standard</div>
            <div class="bg-secondary col-6 py-2">Reduced</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col-6 py-2">Grace Standard</div>
            <div class="bg-secondary col-6 py-2">Grace Reduced</div>
          </div>
        </div>
        <div class="col-1"></div>
      </div>
      <div id="filter" class="row text-center">
        <div class="col-3">
          <div class="row">
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/country" name="Country" placeholder="Country"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/category" name="Category" placeholder="Category"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/country" name="Origin" placeholder="Origin"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" name="Qt" placeholder="Yr"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col py-1">Cost</div>
            <div class="col py-1">Fee</div>
            <div class="bg-secondary col py-1">Cost</div>
            <div class="bg-secondary col py-1">Fee</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col py-1">Cost</div>
            <div class="bg-info col py-1">Fee</div>
            <div class="bg-secondary col py-1">Cost</div>
            <div class="bg-secondary col py-1">Fee</div>
          </div>
        </div>
        <div class="col-1">Currency</div>
        {{-- <div class="col-1">Use after</div>
        <div class="col-1">Use before</div> --}}
      </div>
    </div>
    <div class="card-body pt-2" id="tableList">
      <table class="table table-striped table-hover table-sm">
      @foreach ($fees as $fee)
        <tr class="row" data-resource="/fee/{{ $fee->id }}">
          <td class="col-3">
            <div class="row">
              <div class="col">{{ $fee->for_country }}</div>
              <div class="col">{{ $fee->for_category }}</div>
              <div class="col">{{ $fee->for_origin }}</div>
              <div class="col">{{ $fee->qt }}</div>
            </div>
          </td>
          <td class="col-4">
            <div class="row">
              <div class="col bg-light pe-3"><input type="text" class="form-control noformat text-end" name="cost" value="{{ $fee->cost }}"></div>
              <div class="col bg-light pe-3"><input type="text" class="form-control noformat text-end" name="fee" value="{{ $fee->fee }}"></div>
              <div class="col pe-3"><input type="text" class="form-control noformat text-end" name="cost_reduced" value="{{ $fee->cost_reduced }}"></div>
              <div class="col pe-3"><input type="text" class="form-control noformat text-end" name="fee_reduced" value="{{ $fee->fee_reduced }}"></div>
            </div>
          </td>
          <td class="col-4">
            <div class="row">
              <div class="col bg-light pe-3"><input type="text" class="form-control noformat text-end" name="cost_sup" value="{{ $fee->cost_sup }}"></div>
              <div class="col bg-light pe-3"><input type="text" class="form-control noformat text-end" name="fee_sup" value="{{ $fee->fee_sup }}"></div>
              <div class="col pe-3"><input type="text" class="form-control noformat text-end" name="cost_sup_reduced" value="{{ $fee->cost_sup_reduced }}"></div>
              <div class="col pe-3"><input type="text" class="form-control noformat text-end" name="fee_sup_reduced" value="{{ $fee->fee_sup_reduced }}"></div>
            </div>
          </td>
          <td class="col-1"><input type="text" class="form-control noformat text-center" name="currency" value="{{ $fee->currency }}"></td>
          {{-- <div class="col-1">{{ $fee->use_after }}</div>
          <div class="col-1">{{ $fee->use_before }}</div> --}}
        </tr>
      @endforeach
        <tr>
          <td colspan="13">
            {{ $fees->links() }}
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
