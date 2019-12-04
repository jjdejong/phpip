@extends('layouts.app')

@section('content')

<div class="row card-deck">
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">Categories</span>
        @cannot('client')
        <a href="/matter/create?operation=new" data-target="#ajaxModal" data-toggle="modal" data-size="modal-sm" class="btn btn-primary float-right" title="Create Matter">Create matter</a>
        @endcannot
      </div>
      <div class="card-body pt-0" style="min-height: 80px; max-height: 300px;">
        <table  class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>Count</th>
            <td>
              @cannot('client')
              <span class="float-right text-secondary">New</span>
              @endcannot
            </td>
          </tr>
          @foreach ($categories as $group)
          <tr class="reveal-hidden">
            <td class="py-0">
              <a href="/matter?Cat={{ $group->category_code }}">{{ $group->category }}</a>
            </td>
            <td class="py-0">
              {{ $group->total }}
            </td>
            <td class="py-0">
              @cannot('client')
              <a class="badge badge-primary hidden-action float-right" href="/matter/create?operation=new&category={{$group->category_code}}" data-target="#ajaxModal" title="Create new {{ $group->category }}" data-toggle="modal" data-size="modal-sm">
                &plus;
              </a>
              @endcannot
            </td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    <div class="card border-info mt-1">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">Users tasks</span>
        @cannot('client')
        <button class="btn btn-transparent text-info float-right" disabled>I</button> {{--  This invisible button is only for improving the layout! --}}
        @endcannot
      </div>
      <div class="card-body pt-1" style="min-height: 80px;">
        <table class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>Open</th>
            <th>Hottest</th>
          </tr>
        @foreach ($taskscount as $group)
          @if ($group->no_of_tasks > 0)
          <tr>
            <td>
                <a href="/home?user_dashboard={{ $group->login }}">{{ $group->login }}</a>
            </td>
            <td>
                {{ $group->no_of_tasks }}
            </td>
              @if ($group->urgent_date < now())
            <td class="text-danger">
            @elseif ($group->urgent_date < now()->addWeek())
            <td style="color: purple;">
              @else
            <td>
              @endif
                {{ Carbon\Carbon::parse($group->urgent_date)->isoFormat('L') }}
            </td>
          </tr>
          @endif
        @endforeach
      </table>
      </div>
    </div>
  </div>
  <div class="col-8" id="filter">
    <div class="card border-primary">
      <div class="card-header text-white bg-primary p-1">
        <div class="row">
          <div class="lead col-3">
            Open tasks
          </div>
          @cannot('client')
          <div class="col-5">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-info active">
                <input type="radio" name="my_tasks" id="alltasks" value="0">Everyone
              </label>
              <label class="btn btn-info">
                <input type="radio" name="my_tasks" id="mytasks" value="1">{{ Auth::user()->login }}
              </label>
            </div>
          </div>
          <div class="col-4">
            <div class="input-group">
              <div class="input-group-prepend">
                <button class="btn btn-light" type="button" id="clear-open-tasks">Clear selected on</button>
              </div>
              <input type="text" class="form-control" name="datetaskcleardate" id="taskcleardate" value="{{ now()->format('Y-m-d') }}">
            </div>
          </div>
          @endcannot
        </div>
        <div class="row mt-1">
          <div class="col-6">
          </div>
          <div class="col-3">
            Matter
          </div>
          <div class="col-2">
            Due date
          </div>
          <div class="col-1">
            Clear
          </div>
        </div>
      </div>
      <div class="card-body p-1" id="tasklist" style="min-height: 80px;">
        @isset($tasks)
        <table class="table table-striped table-sm">
          @foreach ($tasks as $task)
          <tr>
            <td class="row py-0">
              <div class="col-6">
                <a href="/matter/{{ $task->matter_id }}/tasks" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" data-resource="/task/" title="All tasks">
                  {{ $task->name }}{{ $task->detail ? " - ".$task->detail : "" }}
                </a>
              </div>
              <div class="col-3">
                <a href="/matter/{{ $task->matter_id }}">
                  {{ $task->uid }}
                </a>
              </div>
          @if ($task->due_date < date('Y-m-d'))
              <div class="col-2 text-danger">
          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
              <div class="col-2" style="color: purple;">
              @else
              <div class="col-2">
              @endif
            {{ $task->due_date }}
              </div>
              <div class="col-1 px-4">
                <input id="{{ $task->id }}" class="clear-open-task" type="checkbox">
              </div>
            </td>
          </tr>
          @endforeach
        </table>
        @else
        <div class="row text-danger">
          The list is empty
        </div>
      @endisset
      </div>
    </div>
    <div class="card border-primary mt-1">
      <div class="card-header text-white bg-primary p-1">
        <div class="row">
          <div class="lead col-3">
            Open renewals
          </div>
          @cannot('client')
          <div class="col-5">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-info active">
                <input type="radio" name="my_renewals" id="allrenewals" value="0">Everyone
              </label>
              <label class="btn btn-info">
                <input type="radio" name="my_renewals" id="myrenewals" value="1">{{ Auth::user()->login }}
              </label>
            </div>
          </div>
          <div class="col-4">
            <div class="input-group">
              <div class="input-group-prepend">
                <button class="btn btn-light" type="button" id="clear-ren-tasks">Clear selected on</button>
              </div>
              <input type="text" class="form-control" name="renewalcleardate" id="renewalcleardate" value="{{ now()->format('Y-m-d') }}">
            </div>
          </div>
          @endcannot
        </div>
        <div class="row mt-1">
          <div class="col-6">
          </div>
          <div class="col-3">
            Matter
          </div>
          <div class="col-2">
            Due date
          </div>
          <div class="col-1">
            Clear
          </div>
        </div>
      </div>

      <div class="card-body p-1" id="renewallist" style="min-height: 80px;">
        @isset($renewals)
        <table class="table table-striped table-sm">
          @foreach ($renewals as $task)
          <tr>
            <td class="row py-0">
              <div class="col-6 pl-4">
                <a href="/matter/{{ $task->matter_id }}/renewals" data-toggle="modal" data-target="#ajaxModal" title="All tasks" data-size="modal-lg">
                  {{ $task->detail }}
                </a>
              </div>
              <div class="col-3">
                <a href="/matter/{{ $task->matter_id }}">
                  {{ $task->uid }}
                </a>
              </div>
          @if ($task->due_date < date('Y-m-d'))
              <div class="col-2 text-danger">
          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
              <div class="col-2" style="color: purple;">
              @else
              <div class="col-2">
              @endif
            {{ $task->due_date }}
              </div>
              <div class="col-1 px-4">
                <input id="{{ $task->id }}" class="clear-ren-task" type="checkbox">
              </div>
            </td>
          </tr>
          @endforeach
        </table>
        @else
        <div class="row text-danger">
          The list is empty
        </div>
      @endisset
      </div>
    </div>
  </div>
</div>

@if (session('status'))
<div class="alert alert-success">
  {{ session('status') }}
</div>
@endif

@stop

@section('script')

@include('home-js')

@stop
