@extends('layouts.app')

@section('titre')
    Actors edition
@endsection

@section('style')

<style>

.reveal-hidden:hover .hidden-action {
    display: inline-block;
</style>

@stop

@section('content')
<div id="events-tab">Event names</div>
<a href="eventname/create"  data-toggle="modal" data-target="#addModal" data-remote="false" title="Event name" data-resource="/eventname/create/" data-source="/eventname?">Add a new event name</a>
<div id="events-box">
<table class="table table-striped table-hover table-sm">
  <thead>
    <tr>
<th>Code</th>
        <th>Name</th>
        <th>Notes</th>
        <th>Delete</th>
    </tr>
    <tr id="filter">
        <th><input class="filter-input form-control form-control-sm" data-source="/eventname?" name="Code" placeholder="Code" value="{{ old('Code') }}"></th>
        <th><input class="filter-input form-control form-control-sm" data-source="/eventname?" name="Name" placeholder="Name" value="{{ old('Name') }}"></th>
        <th></th>
        <th></th>
    </tr>
  </thead>
<div id="event-table-list">
<div class="phpip-list" id="events-list">
  <tbody id="rule-list">

@foreach ($enameslist as $event)
    <tr class="rule-list-row" data-id="{{ $event->code }}">
        <td><a href="/eventname/{{ $event->code }}" class="hidden-action" data-source="/eventname?" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Event name info" data-resource="/eventname/">
                                {{ $event->code }}</a></td>
        <td>{{ $event->name }}</td>
        <td>{{ $event->notes }}</td>
        <td>
            <span class="delete-event-name float-right text-danger ui-icon ui-icon-trash" data-source="/eventname?" data-id="{{ $event->code }}" title="Delete event" />
        </td>
    </tr>
@endforeach
  </tbody>
</table>
</div>
</div>
</div>

<!-- Modals -->
@include('partials.table-show-modals')
@include('partials.table-add-modals')

@endsection

@section('script')

@include('tables.table-js')

@stop
