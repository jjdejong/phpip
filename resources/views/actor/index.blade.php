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
<div class="btn btn-info">
<a href="actor/create"  data-toggle="modal" data-target="#addModal" data-remote="false" title="Actor data" data-source="/actor?" data-resource="/actor/">Add a new actor</a>
</div>
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
    	<th><div class="btn-group btn-group-toggle" data-toggle="buttons">
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
<div id="actor-table-list">
<div id="actors-list">
  <tbody id="actor-list">

@foreach ($actorslist as $actor)
    <tr class="actor-list-row" data-id="{{ $actor->id }}">
    	<td><a href="/actor/{{ $actor->id }}" class="hidden-action" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Actor data" data-resource="/actor/" data-source="/actor?">
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
    		<span class="delete-from-list float-right" data-id="{{ $actor->id }}" title="Delete actor"> &#10060;</span>
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
