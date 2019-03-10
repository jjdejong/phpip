@extends('layouts.app')

@section('content')
<div id="events-tab">Event names</div>
<a href="eventname/create" data-toggle="modal" data-target="#addModal" data-remote="false" title="Event name" data-resource="/eventname/create/">Add a new event name</a>
  <table class="table table-striped table-hover table-sm">
    <thead>
      <tr>
        <th>Code</th>
        <th>Name</th>
        <th>Notes</th>
        <th></th>
      </tr>
      <tr id="filter">
        <th><input class="filter-input form-control form-control-sm" data-source="/eventname?" name="Code" placeholder="Code" value="{{ old('Code') }}"></th>
        <th><input class="filter-input form-control form-control-sm" data-source="/eventname?" name="Name" placeholder="Name" value="{{ old('Name') }}"></th>
        <th colspan="2"></th>
      </tr>
    </thead>

    <tbody id="rule-list">

      @foreach ($enameslist as $event)
      <tr class="rule-list-row reveal-hidden" data-id="{{ $event->code }}">
        <td><a href="/eventname/{{ $event->code }}" data-source="/eventname?" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Event name info" data-resource="/eventname/">
            {{ $event->code }}</a></td>
        <td>{{ $event->name }}</td>
        <td>{{ $event->notes }}</td>
        <td>
          <a class="delete-event-name float-right text-danger hidden-action" data-source="/eventname?" data-id="{{ $event->code }}" title="Delete event" href="javascript:void(0);">&times;</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

<!-- Modals -->
@include('partials.table-show-modals')
<div id="addModal" class="modal fade" role="dialog">
  @include('partials.generic-modals')
</div>

@endsection

@section('script')

@include('tables.table-js')

@stop
