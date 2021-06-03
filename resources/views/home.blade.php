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

<div class="row card-deck">
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header text-white bg-info p-1">
        <span class="lead">Categories</span>
        @canany(['admin', 'readwrite'])
        <a href="/matter/create?operation=new" data-target="#ajaxModal" data-toggle="modal" data-size="modal-sm" class="btn btn-primary float-right" title="Create Matter">Create matter</a>
        @endcanany
      </div>
      <div class="card-body pt-0">
        <table  class="table table-striped table-sm">
          <tr>
            <th></th>
            <th>Count</th>
            <td>
              @canany(['admin', 'readwrite'])
              <span class="float-right text-secondary">New</span>
              @endcanany
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
              @canany(['admin', 'readwrite'])
              <a class="text-primary hidden-action float-right" href="/matter/create?operation=new&category={{$group->category_code}}" data-target="#ajaxModal" title="Create {{ $group->category }}" data-toggle="modal" data-size="modal-sm">
                <i class="bi-plus-circle-fill"></i>
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
        <span class="lead">Users tasks</span>
        @canany(['admin', 'readwrite'])
        <button class="btn btn-transparent text-info float-right" disabled>I</button> {{--  This invisible button is only for improving the layout! --}}
        @endcanany
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
      @livewire('dashboard-tasks', ['isrenewals' => 0])
      @livewire('dashboard-tasks', ['isrenewals' => 1])
  </div>
</div>

@if (session('status'))
<div class="alert alert-success">
  {{ session('status') }}
</div>
@endif

@endsection
