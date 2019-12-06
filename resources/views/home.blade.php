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
      <div class="card-body pt-0">
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
      <div class="card-body pt-1">
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
            @if(!Request::filled('user_dashboard'))
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-info active">
                <input type="radio" name="my_tasks" id="alltasks" value="0">Everyone
              </label>
              <label class="btn btn-info">
                <input type="radio" name="my_tasks" id="mytasks" value="1">{{ Auth::user()->login }}
              </label>
            </div>
            @endif
          </div>
          <div class="col-4">
            <div class="input-group">
              <div class="input-group-prepend">
                <button class="btn btn-light" type="button" id="clearOpenTasks">Clear selected on</button>
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
      <div class="card-body p-1" id="tasklist">
        @include('task.index', ['isrenewals' => '0'])
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
            @if(!Request::filled('user_dashboard'))
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-info active">
                <input type="radio" name="my_renewals" id="allrenewals" value="0">Everyone
              </label>
              <label class="btn btn-info">
                <input type="radio" name="my_renewals" id="myrenewals" value="1">{{ Auth::user()->login }}
              </label>
            </div>
            @endif
          </div>
          <div class="col-4">
            <div class="input-group">
              <div class="input-group-prepend">
                <button class="btn btn-light" type="button" id="clearRenewals">Clear selected on</button>
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

      <div class="card-body p-1" id="renewallist">
        @php $tasks = $renewals @endphp
        @include('task.index', ['isrenewals' => '1'])
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
