@extends('layouts.app')

@section('style')
<style type="text/css">
  .card-body {
      max-height: 350px;
      min-height: 80px;
      overflow: auto;
  }
</style>
@endsection

@section('content')

<div class="row">
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">{{ _i("Categories") }}</span>
        @canany(['admin', 'readwrite'])
        <a href="/matter/create?operation=new" data-bs-target="#ajaxModal" data-bs-toggle="modal" data-size="modal-sm" class="btn btn-primary float-end" title="Create Matter">Create matter</a>
        @endcanany
      </div>
      <div class="card-body pt-0">
        <table  class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>{{ _i("Count") }}</th>
            <td>
              @canany(['admin', 'readwrite'])
              <span class="float-end text-secondary">New</span>
              @endcanany
            </td>
          </tr>
          @foreach ($categories as $group)
          <tr class="reveal-hidden">
            <td class="py-0">
              <a href="/matter?Cat={{ $group->category_code }}">{{ _i($group->category) }}</a>
            </td>
            <td class="py-0">
              {{ $group->total }}
            </td>
            <td class="py-0">
              @canany(['admin', 'readwrite'])
              <a class="badge text-bg-primary hidden-action float-end" href="/matter/create?operation=new&category={{$group->category_code}}" data-bs-target="#ajaxModal" title="Create {{ $group->category }}" data-bs-toggle="modal" data-size="modal-sm">
                &plus;
              </a>
              @endcanany
            </td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    <div class="card border-info mt-1">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">{{ _i("Users tasks") }}</span>
        @canany(['admin', 'readwrite'])
        <button class="btn btn-transparent text-info float-end" disabled>I</button> {{--  This invisible button is only for improving the layout! --}}
        @endcanany
      </div>
      <div class="card-body pt-1">
        <table class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>{{ _i("Open") }}</th>
            <th>{{ _i("Hottest") }}</th>
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
        <form class="row">
          <div class="lead col-2">
            {{ _i("Open tasks") }}
          </div>
          @cannot('client')
          <div class="col-6">
            <div class="input-group">
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="alltasks" value="0">Everyone
              </label>
              @if(!Request::filled('user_dashboard'))
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="mytasks" value="1">{{ Auth::user()->login }}
              </label>
              @endif
              <label class="btn btn-info">
                <input type="radio" class="btn-check" name="what_tasks" id="clientTasks" value="2">Client
              </label>
              <input type="hidden" id="clientId" name="client_id">
              <input type="text" class="form-control me-3" data-ac="/actor/autocomplete" data-actarget="client_id" placeholder="Select Client">
            </div>
          </div>
          <div class="col-4">
            <div class="input-group">
              @canany(['admin', 'readwrite'])
              <button class="btn btn-light" type="button" id="clearOpenTasks">Clear selected on</button>
              <input type="text" class="form-control me-2" name="datetaskcleardate" id="taskcleardate" value="{{ now()->isoFormat('L') }}">
              @endcanany
            </div>
          </div>
          @endcannot
        </form>
        <div class="row mt-1 g-0">
          <div class="col">
          </div>
          <div class="col-2">
            {{ _i("Matter") }}
          </div>
          <div class="col">
            {{ _i("Description") }}
          </div>
          <div class="col-2">
            {{ _i("Due date") }}
          </div>
          @canany(['admin', 'readwrite'])
          <div class="col-1">
            {{ _i("Clear") }}
          </div>
          @endcanany
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
            {{ _i("Open renewals") }}
          </div>
          @canany(['admin', 'readwrite'])
          <div class="col">
            <div class="input-group">
              <button class="btn btn-light" type="button" id="clearRenewals">Clear selected on</button>
              <input type="text" class="form-control me-2" name="renewalcleardate" id="renewalcleardate" value="{{ now()->isoFormat('L') }}">
            </div>
          </div>
          @endcanany
        </div>
        <div class="row mt-1 g-0">
          <div class="col">
          </div>
          <div class="col-2">
            {{ _i("Matter") }}
          </div>
          <div class="col">
            {{ _i("Description") }}
          </div>
          <div class="col-2">
            {{ _i("Due date") }}
          </div>
          @canany(['admin', 'readwrite'])
          <div class="col-1">
            {{ _i("Clear") }}
          </div>
          @endcanany
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
