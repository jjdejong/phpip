@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  {{ __('Countries') }}
  <a href="countries/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Country') }}" data-resource="/countries/">{{ __('Create Country') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th style="width: 80px;">
              <div class="input-group input-group-sm" style="width: 80px;">
                <input class="form-control" data-source="/countries" name="iso" placeholder="{{ __('ISO') }}" style="width: 50px;">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="iso">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th style="width: 200px;">
              <div class="input-group input-group-sm" style="width: 200px;">
                <input class="form-control" data-source="/countries" name="name" placeholder="{{ __('Name') }}" style="width: 170px;">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="name">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th class="text-center" style="width: 60px;">{{ __('EP') }}</th>
            <th class="text-center" style="width: 60px;">{{ __('WO') }}</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($countries as $country)
          <tr class="reveal-hidden" data-id="{{ $country->iso }}">
            <td>
              <a href="{{ url('countries/' . $country->iso) }}" data-panel="ajaxPanel" title="{{ __('Country info') }}">
                {{ $country->iso }}
              </a>
            </td>
            <td>{{ $country->getTranslation('name', app()->getLocale()) }}
            </td>
            <td class="text-center">
              @if($country->ep == 1)
                <svg class="text-success" width="16" height="16" fill="currentColor" title="{{ __('EP Member') }}">
                  <use xlink:href="#check-circle-fill"/>
                </svg>
              @endif
            </td>
            <td class="text-center">
              @if($country->wo == 1)
                <svg class="text-success" width="16" height="16" fill="currentColor" title="{{ __('PCT Member') }}">
                  <use xlink:href="#check-circle-fill"/>
                </svg>
              @endif
            </td>
          </tr>
          @endforeach
          <tr>
            <td colspan="4">
              {{ $countries->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('Country information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on country to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
