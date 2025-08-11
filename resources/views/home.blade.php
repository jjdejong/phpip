@extends('layouts.app')

@section('style')
<style type="text/css">
  .card-body {
      max-height: 350px;
      min-height: 80px;
      overflow: auto;
  }
  input[data-ac] {
      position: relative;
  }
  input[data-ac] + .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 1050;
      min-width: 200px;
      margin-top: 0.125rem;
  }
</style>
@endsection

@section('content')

<div class="row">
  <div class="col-4" id="leftPanels">
    <div class="card border-info">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">{{ __('Categories') }}</span>
        @can('readwrite')
        <a href="/matter/create?operation=new" data-bs-target="#ajaxModal" data-bs-toggle="modal" data-size="modal-sm" class="btn btn-primary float-end" title="{{ __('Create Matter') }}">{{ __('Create matter') }}</a>
        @endcan
      </div>
      <div id="categoriesList" class="card-body pt-0">
        <table  class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>{{ __('Count') }}</th>
            <td>
              @can('readwrite')
              <span class="float-end text-secondary">{{ __('New') }}</span>
              @endcan
            </td>
          </tr>
          @foreach ($categories as $group)
          <tr class="reveal-hidden">
            <td class="py-0">
              <a href="/matter?Cat={{ $group->code }}">{{ $group->category }}</a>
            </td>
            <td class="py-0">
              {{ $group->total }}
            </td>
            <td class="py-0">
              @can('readwrite')
              <a class="hidden-action float-end" href="/matter/create?operation=new&category={{$group->code}}" data-bs-target="#ajaxModal" title="Create {{ $group->category }}" data-bs-toggle="modal" data-size="modal-sm">
                <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#plus-circle-fill"/></svg>
              </a>
              @endcan
            </td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    <div class="card border-info mt-1">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">{{ __('Users tasks') }}</span>
        @can('readwrite')
        <button class="btn btn-transparent text-info float-end" disabled>I</button> {{--  This invisible button is only for improving the layout! --}}
        @endcan
      </div>
      <div class="card-body pt-1" id="usersTasksPanel">
        <table class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>{{ __('Open') }}</th>
            <th>{{ __('Hottest') }}</th>
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
            <td class="table-danger">
            @elseif ($group->urgent_date < now()->addWeeks(2))
            <td class="table-warning">
              @else
            <td>
              @endif
                {{ \Carbon\Carbon::parse($group->urgent_date)->isoFormat('L') }}
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
        <form class="row">
          <div class="lead col-2">
            {{ __('Open tasks') }}
          </div>
          @can('readonly')
          <div class="col-6">
            <div class="input-group">
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="alltasks" value="0">{{ __('Everyone') }}
              </label>
              @if(!Request::filled('user_dashboard'))
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="mytasks" value="1">{{ Auth::user()->login }}
              </label>
              @endif
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="clientTasks" value="2">{{ __('Client') }}
              </label>
              <input type="hidden" id="clientId" name="client_id">
              <input type="text" class="form-control me-3" data-ac="/actor/autocomplete" data-actarget="client_id" placeholder="{{ __('Select Client') }}">
            </div>
          </div>
          <div class="col-4">
            <div class="input-group">
              @can('readwrite')
              <button class="btn btn-light" type="button" id="clearOpenTasks">{{ __('Clear selected on') }}</button>
              <input type="text" class="form-control me-2" name="datetaskcleardate" id="taskcleardate" value="{{ now()->isoFormat('L') }}">
              @endcan
            </div>
          </div>
          @endcan
        </form>
        <div class="row mt-1 g-0">
          <div class="col">
          </div>
          <div class="col-2">
            {{ __('Matter') }}
          </div>
          <div class="col">
            {{ __('Description') }}
          </div>
          <div class="col-2">
            {{ __('Due date') }}
          </div>
          @can('readwrite')
          <div class="col-1">
            {{ __('Clear') }}
          </div>
          @endcan
        </div>
      </div>
      <div class="card-body p-1" id="tasklist">
        {{-- Placeholder --}}
      </div>
    </div>
    <div class="card border-primary mt-1">
      <div class="card-header text-white bg-primary p-1">
        <div class="row">
          <div class="lead col-8">
            {{ __('Open renewals') }}
          </div>
          @can('readwrite')
          <div class="col">
            <div class="input-group">
              <button class="btn btn-light" type="button" id="clearRenewals">{{ __('Clear selected on') }}</button>
              <input type="text" class="form-control me-2" name="renewalcleardate" id="renewalcleardate" value="{{ now()->isoFormat('L') }}">
            </div>
          </div>
          @endcan
        </div>
        <div class="row mt-1 g-0">
          <div class="col">
          </div>
          <div class="col-2">
            {{ __('Matter') }}
          </div>
          <div class="col">
            {{ __('Description') }}
          </div>
          <div class="col-2">
            {{ __('Due date') }}
          </div>
          @can('readwrite')
          <div class="col-1">
            {{ __('Clear') }}
          </div>
          @endcan
        </div>
      </div>

      <div class="card-body p-1" id="renewallist">
        {{-- Placeholder --}}
      </div>
    </div>
  </div>
</div>

@if (session('status'))
<div class="alert alert-success">
  {{ session('status') }}
</div>
@endif

@endsection

@section('script')
<script src="{{ asset('js/home.js') }}" defer></script>
@endsection
