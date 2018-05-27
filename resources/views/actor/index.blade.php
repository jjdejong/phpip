@extends('layouts.app')

@section('titre')
    Actors edition
@endsection

@section('style')

<style>

.reveal-hidden:hover .hidden-action {
	display: inline-block;
}
.noformat {
    border: none;
    background: white;
    color: inherit;
    padding: 0px;
    height: inherit;
    display: inline;
    box-shadow: none;
}
</style>

@stop


@section('content')
<div id="rules-tab"><h1>Actors</h1></div>
<a href="actor/create"  data-toggle="modal" data-target="#addModal" data-remote="false" title="Actor data" data-source="/actor?" data-resource="/actor/">Add a new actor</a>
<div id="rules-box">
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
    	<th></th>
    	<th></th>
    	<th></th>
    	<th><input class="filter-input" type="radio" name="phy_person" value="1" />Physical&nbsp;
                        <input class="filter-input" type="radio" name="phy_person" value="0" />Legal&nbsp;
                        <input class="filter-input" type="radio" name="phy_person" value="" checked=checked />Both</th>
    	<th></th>
    </tr>
  </thead>
<div id="actor-table-list">
<div id="actors-list">
  <tbody id="actor-list">

@foreach ($actorslist as $actor)
    <tr class="actor-list-row" data-id="{{ $actor->id }}">
    	<td class="col-name"><a href="/actor/{{ $actor->id }}" class="hidden-action" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Actor data" data-resource="/actor/" data-source="/actor?">
								{{ $actor->name }}</a></td>
    	<td class="col-trigger">{{ $actor->first_name }}</td>
    	<td class="col-category">{{ $actor->display_name }}</td>
    	<td class="col-country">{{ empty($actor->company) ? '' : $actor->company->name }}</td>
    	<td class="col-country">
			@if ($actor->phy_person) 
			  Physical
			@else
			  Legal
			@endif
		</td>
    	<td class="col-delete" >
    		<span class="delete-from-list float-right text-danger" data-id="{{ $actor->id }}" title="Delete actor">&ominus;</span>
    	</td>
    </tr>
@endforeach
  </tbody>
</table>
</div>
</div>
</div>

<!-- Modals -->
@include('partials.table-show-modals')
@include('partials.table-add-modals')

@endsection

@section('script')

@include('actor.actor-js')

@stop
