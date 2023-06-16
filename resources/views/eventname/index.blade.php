@extends('layouts.app')

@section('content')
<legend class="text-primary">
  {{ _i("Event Names") }}
  <a href="eventname/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="{{ _i('Event name') }}" data-resource="/eventname/">{{ _i('Create a new event name') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary overflow-auto" style="max-height: 640px;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/eventname" name="Code" placeholder="{{ _i('Code') }}"></th>
            <th class="border-top-0"><input class="filter-input form-control form-control-sm" data-source="/eventname" name="Name" placeholder="{{ _i('Name') }}"></th>
            <th class="align-middle text-center border-top-0" colspan="2">{{ _i("Notes") }}</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($enameslist as $event)
          <tr class="reveal-hidden" data-id="{{ $event->code }}">
            <td>
              <a href="/eventname/{{ $event->code }}" data-panel="ajaxPanel" title="{{ _i('Event name info') }}">
                {{ $event->code }}
              </a>
            </td>
            <td>{{ $event->name }}</td>
            <td>{{ $event->notes }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ _i('Event name information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ _i('Click on event name to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

@include('tables.table-js')

@endsection
