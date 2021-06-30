@extends('layouts.app')

@section('content')
<legend class="text-light">
  Renewal logs
</legend>
<div class="row">
  <div class="col">
    <div class="card">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="bg-primary text-light">
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Matter" placeholder="Matter"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Client" placeholder="Client"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="Job" placeholder="Job"></th>
            <th><input class="filter-input form-control form-control-sm" data-source="/logs" name="User" placeholder="User"></th>
            <th>
              <input type="date" class="form-control form-control-sm" name="Fromdate" id="Fromdate" title="From selected date" value="{{ Request::get('Fromdate') }}">
              <input type="date" class="form-control form-control-sm" name="Untildate" id="Untildate" title="Until selected date" value="{{ Request::get('Untildate') }}">
            </th>
            <th>Qt</th>
            <th>Steps</th>
            <th>Grace</th>
            <th>Invoicing</th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($logs as $log)
          <tr data-id="{{ $log->id }}" class="reveal-hidden">
            <td>
                <a href="/matter/{{ $log->task->matter->id }}">
                  {{ $log->task->matter->uid }}
                </a>
            </td>
            <td>{{ $log->task->matter->client->name }}</td>
            <td>{{ $log->job_id }}</td>
            <td>{{ $log->creatorInfo->name }}</td>
            <td>{{ $log->created_at }}</td>
            <td>{{ $log->task->detail }}</td>
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
