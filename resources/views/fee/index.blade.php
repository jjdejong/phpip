@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    <span>
      {{ __('Fees') }}
      <a class="text-primary" href="https://github.com/jjdejong/phpip/wiki/Renewal-Management#costs-and-fees" target="_blank">
        <svg width="16" height="16" fill="currentColor"><use xlink:href="#question-circle-fill"/></svg>
      </a>
    </span>
    <a href="fee/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('New line') }}" data-resource="/fee/">{{ __('Add a new line') }}</a>
  </legend>
  <div class="card border-primary">
    <div class="card-header bg-primary text-light py-1">
      <div class="row text-center">
        <div class="col-3 align-bottom"></div>
        <div class="col-4">
          <div class="row">
            <div class="col-6 py-2">{{ __('Standard') }}</div>
            <div class="bg-secondary col-6 py-2">{{ __('Reduced') }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col-6 py-2">{{ __('Grace Standard') }}</div>
            <div class="bg-secondary col-6 py-2">{{ __('Grace Reduced') }}</div>
          </div>
        </div>
        <div class="col-1"></div>
      </div>
      <div id="filter" class="row text-center">
        <div class="col-3">
          <div class="row">
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/country" name="Country" placeholder="{{ __('Country') }}"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/category" name="Category" placeholder="{{ __('Category') }}"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" data-source="/country" name="Origin" placeholder="{{ __('Origin') }}"></div>
            <div class="col ps-0"><input class="form-control form-control-sm" name="Qt" placeholder="{{ __('Yr') }}"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col py-1">{{ __('Cost') }}</div>
            <div class="col py-1">{{ __('Fee') }}</div>
            <div class="bg-secondary col py-1">{{ __('Cost') }}</div>
            <div class="bg-secondary col py-1">{{ __('Fee') }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="bg-info col py-1">{{ __('Cost') }}</div>
            <div class="bg-info col py-1">{{ __('Fee') }}</div>
            <div class="bg-secondary col py-1">{{ __('Cost') }}</div>
            <div class="bg-secondary col py-1">{{ __('Fee') }}</div>
          </div>
        </div>
        <div class="col-1">{{ __('Currency') }}</div>
        {{-- <div class="col-1">{{ __('Use after') }}</div>
        <div class="col-1">{{ __('Use before') }}</div> --}}
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
              <div class="col pe-3"><input class="form-control noformat text-end" name="cost" value="{{ $fee->cost }}"></div>
              <div class="col pe-3"><input class="form-control noformat text-end" name="fee" value="{{ $fee->fee }}"></div>
              <div class="col pe-3" title="{{ __('Leave empty if not used') }}"><input class="form-control noformat text-end" name="cost_reduced" value="{{ $fee->cost_reduced }}"></div>
              <div class="col pe-3" title="{{ __('Leave empty if not used') }}"><input class="form-control noformat text-end" name="fee_reduced" value="{{ $fee->fee_reduced }}"></div>
            </div>
          </td>
          <td class="col-4">
            <div class="row" title="{{ __('Leave empty if not used') }}">
              <div class="col pe-3"><input class="form-control noformat text-end" name="cost_sup" value="{{ $fee->cost_sup }}"></div>
              <div class="col pe-3"><input class="form-control noformat text-end" name="fee_sup" value="{{ $fee->fee_sup }}"></div>
              <div class="col pe-3"><input class="form-control noformat text-end" name="cost_sup_reduced" value="{{ $fee->cost_sup_reduced }}"></div>
              <div class="col pe-3"><input class="form-control noformat text-end" name="fee_sup_reduced" value="{{ $fee->fee_sup_reduced }}"></div>
            </div>
          </td>
          <td class="col-1 bg-transparent"><input class="form-control noformat text-center" name="currency" value="{{ $fee->currency }}"></td>
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
