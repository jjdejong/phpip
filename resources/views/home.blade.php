@extends('layouts.app')

@section('content')

<div class="row card-deck">
  <div class="col-4">
    <div class="card">
      <div class="card-header py-1">
        <div class="row">
          <div class="lead col-8">
            Categories
          </div>
          <div class="col-4">
            <a href="/matter/create?operation=new" data-target="#ajaxModal" data-toggle="modal" data-size="modal-sm" class="btn btn-primary" title="Create Matter">Create matter</a>
          </div>
        </div>
        <div class="row font-weight-bold">
          <div class="col-8">
            Category
          </div>
          <div class="col-3">
            Count
          </div>
          <div class="col-1">
          </div>
        </div>
      </div>

      <div class="card-body pt-1">
        @foreach ($categories as $group)
        <div class="row reveal-hidden">
          <div class="col-8">
            {{$group->category }}
          </div>
          <div class="col-3">
            {{ $group->total }}
          </div>
          <div class="col-1">
            <a class="badge badge-primary hidden-action" href="/matter/create?operation=new&category={{$group->category_code}}" data-target="#ajaxModal" title="Create new {{ $group->category }}" data-toggle="modal" data-size="modal-sm">
              &plus;
            </a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    <div class="card mt-1">
      <div class="card-header">
        <div class="row">
          <div class="lead col-12">
            Users tasks
          </div>
        </div>
        <div class="row font-weight-bold">
          <div class="col-6">
            User
          </div>
          <div class="col-3">
            Open tasks
          </div>
          <div class="col-3">
            Most urgent
          </div>
        </div>
      </div>

      <div class="card-body pt-1">
        @foreach ($taskscount as $group)
        @if ($group->no_of_tasks > 0)
        <div class="row">
          <div class="col-6">
            {{$group->login }}
          </div>
          <div class="col-3">
            {{ $group->no_of_tasks }}
          </div>
          @if ($group->posix_urgent_date < date('Y-m-d'))
          <div class="col-3 text-danger">
            {{ $group->urgent_date }}
          </div>
          @elseif ($group->posix_urgent_date < date('Y-m-d', strtotime("+1 week")))
          <div class="col-3">
            <font color="purple">{{ $group->urgent_date }}</font>
          </div>
          @else
          <div class="col-3">
            {{ $group->urgent_date }}
          </div>
          @endif
        </div>
        @endif
      @endforeach
      </div>
    </div>
  </div>
  <div class="col-8" id="filter">
    <div class="card">
      <div class="card-header py-1">
        <div class="row">
          <div class="lead col-3">
            Open tasks
          </div>
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
                <button class="btn btn-outline-primary" type="button" id="clear-open-tasks">Clear selected on</button>
              </div>
              <input type="date" class="form-control" name="datetaskcleardate" id="taskcleardate">
            </div>
          </div>
        </div>
        <div class="row font-weight-bold">
          <div class="col-6">
            Tasks
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
      <div class="card-body pt-1" id="tasklist">
        @if (is_array($tasks) )
        @foreach ($tasks as $task)
        <div class="row">
          <div class="col-6">
            <a href="/matter/{{ $task->trigger->matter->id }}/tasks" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" data-resource="/task/" title="All tasks">
              {{ $task->info->name }}{{ $task->detail ? " - ".$task->detail : "" }}
            </a>
          </div>
          <div class="col-3">
            <a href="/matter/{{ $task->trigger->matter->id }}">
              {{ $task->trigger->matter->uid }}
            </a>
          </div>
          @if ($task->due_date < date('Y-m-d'))
          <div class="col-2 text-danger">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
          <div class="col-2">
            <font color="purple">{{ date_format(date_create($task->due_date), 'd/m/Y') }}</font>
          </div>
          @else
          <div class="col-2">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @endif
          <div class="col-1">
            <input id="{{ $task->id }}" class="clear-open-task" type="checkbox">
          </div>
        </div>
        @endforeach
        @else
        <div class="row text-danger">
          The list is empty
        </div>
        @endif
      </div>
    </div>
    <div class="card mt-1">
      <div class="card-header py-1">
        <div class="row">
          <div class="lead col-3">
            Open renewals
          </div>
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
                <button class="btn btn-outline-primary" type="button" id="clear-ren-tasks">Clear selected on</button>
              </div>
              <input type="date" class="form-control" name="renewalcleardate" id="renewalcleardate">
            </div>
          </div>
        </div>
        <div class="row font-weight-bold">
          <div class="col-6">
            Renewals
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

      <div class="card-body pt-1" id="renewallist">
        @if (is_array($renewals) )
        @foreach ($renewals as $task)
        <div class="row">
          <div class="col-6">
            <a href="/matter/{{ $task->trigger->matter->id }}/renewals" data-toggle="modal" data-target="#ajaxModal" title="All tasks" data-size="modal-lg">
              {{ $task->detail }}
            </a>
          </div>
          <div class="col-3">
            <a href="/matter/{{ $task->trigger->matter->id }}">
              {{ $task->trigger->matter->uid }}
            </a>
          </div>
          @if ($task->due_date < date('Y-m-d'))
          <div class="col-2 text-danger">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
          <div class="col-2">
            <font color="purple">{{ date_format(date_create($task->due_date), 'd/m/Y') }}</font>
          </div>
          @else
          <div class="col-2">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @endif
          <div class="col-1">
            <input id="{{ $task->id }}" class="clear-ren-task" type="checkbox">
          </div>
        </div>
        @endforeach
        @else
        <div class="row text-danger">
          The list is empty
        </div>
        @endif
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
