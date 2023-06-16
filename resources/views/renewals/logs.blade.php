@extends('layouts.app')

@section('content')
<legend class="text-light">
  {{ _i('Renewal logs') }}
</legend>
<div class="row">
  <div class="col">
    <div class="card">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Matter" placeholder="{{ _i('Matter') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Client" placeholder="{{ _i('Client') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Job" placeholder="{{ _i('Job') }}"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="User" placeholder="{{ _i('User') }}"></th>
            <th>
              <input type="date" class="form-control form-control-sm" name="Fromdate" id="Fromdate" title="{{ _i('From selected date') }}" value="{{ Request::get('Fromdate') }}">
              <input type="date" class="form-control form-control-sm" name="Untildate" id="Untildate" title="{{ _i('Until selected date') }}" value="{{ Request::get('Untildate') }}">
            </th>
            <th>{{ _i('Qt') }}</th>
            <th>{{ _i('Steps') }}</th>
            <th>{{ _i('Grace') }}</th>
            <th>{{ _i('Invoicing') }}</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($logs as $log)
          <tr data-id="{{ $log->id }}" class="reveal-hidden">
            <td>
                @if( is_null($log->task))
                {{ _i('Task deleted') }}
                @else
                <a href="/matter/{{$log->task->matter->id}}">{{$log->task->matter->uid}}</a>
                @endif
            </td>
            <td>{{ is_null($log->task) ? _i('Task deleted') : $log->task->matter->client->name }}</td>
            <td>{{ $log->job_id }}</td>
            <td>{{ $log->creatorInfo->name }}</td>
            <td>{{ $log->created_at }}</td>
            <td>{{ is_null($log->task) ? '' : $log->task->detail }}</td>
            <td>{{ is_null($log->from_step) ? '' : $log->from_step ." -> ". $log->to_step }}</td>
            <td>{{ is_null($log->from_grace) ? '' : $log->from_grace ." -> ". $log->to_grace }}</td>
            <td>{{ is_null($log->from_invoice) ? '' : $log->from_invoice ." -> ". $log->to_invoice }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $logs->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('script')

@include('tables.table-js')

@endsection
