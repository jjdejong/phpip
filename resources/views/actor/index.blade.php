@extends('layouts.app')

@section('content')
<h1>Actors</h1>
<div class="btn btn-info">
  <a href="actor/create" data-toggle="modal" data-target="#addModal" data-remote="false" title="Actor data" data-resource="/actor/">Add a new actor</a>
</div>
<table class="table table-striped table-hover table-sm">
  <thead>
    <tr>
      <th>Name</th>
      <th>First name</th>
      <th>Display name</th>
      <th>Company</th>
      <th>Person</th>
      <th>Delete</th>
    </tr>
    <tr id="filter" class="sticky-top">
      <th><input class="filter-input form-control form-control-sm" name="Name" value="{{ old('Name') }}"></th>
      <th colspan="3"></th>
      <th>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-info">
            <input type="radio" name="phy_person" id="physical" value="1" />Physical
          </label>
          <label class="btn btn-info">
            <input type="radio" name="phy_person" id="legal" value="0" />Legal
          </label>
          <label class="btn btn-info active">
            <input type="radio" name="phy_person" id="both" value="" />Both
          </label>
        </div>
      <th></th>
    </tr>
  </thead>
  <tbody id="actor-list">
    @foreach ($actorslist as $actor)
    <tr class="actor-list-row reveal-hidden" data-id="{{ $actor->id }}">
      <td><a href="/actor/{{ $actor->id }}" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Actor data" data-resource="/actor/">
          {{ $actor->name }}</a></td>
      <td>{{ $actor->first_name }}</td>
      <td>{{ $actor->display_name }}</td>
      <td>{{ empty($actor->company) ? '' : $actor->company->name }}</td>
      <td>
        @if ($actor->phy_person)
        Physical
        @else
        Legal
        @endif
      </td>
      <td>
        <a class="delete-from-list float-right text-danger hidden-action" data-id="{{ $actor->id }}" title="Delete actor" href="javascript:void(0);">&times;</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<!-- Modals -->
@include('partials.table-show-modals')

<div id="usedModal" class="modal fade" role="dialog">
  @include('partials.generic-modals')
</div>
<div id="addModal" class="modal fade" role="dialog">
  @include('partials.generic-modals')
</div>

@endsection

@section('script')

@include('actor.actor-js')

@stop
