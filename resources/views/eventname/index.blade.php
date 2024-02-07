@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  Event Names
  <a href="eventname/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="Event name" data-resource="/eventname/">Create Event Name</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1" style="max-height: 640px; overflow: auto;">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control" data-source="/eventname" name="Code" placeholder="Code"></th>
            <th><input class="form-control" data-source="/eventname" name="Name" placeholder="Name"></th>
            <th class="text-center" colspan="2">Notes</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($enameslist as $event)
          <tr class="reveal-hidden" data-id="{{ $event->code }}">
            <td>
              <a href="/eventname/{{ $event->code }}" data-panel="ajaxPanel" title="Event name info">
                {{ $event->code }}
              </a>
            </td>
            <td>{{ $event->name }}</td>
            <td>{{ $event->notes }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $enameslist->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-5">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        Event name information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on event name to view and edit details
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
