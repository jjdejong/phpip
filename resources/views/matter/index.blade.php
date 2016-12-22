@extends('layouts.app')

@section('content')

	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>Ref</th>
			<th>Cat</th>
			<th>ID</th>
			<th>Cont.</th>
			<th>Parent</th>
			<th>Responsable</th>
			<th>Updater</th>
			<th>Updated</th>
			<th>Expires</th>
			<th>Notes</th>
		</tr>
		<tr>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
			<td><input class="form-control input-sm" placeholder="Filter"></td>
		</tr>
		@foreach ($matters as $matter)
			@if ($matter->container_ID)
				<tr>
			@else
				<tr class="info"> 
			@endif
				<td>{{ $matter->caseref . $matter->country }}</td>
				<td>{{ $matter->category_code }}</td>
				<td>{{ $matter->ID }}</td>
				<td>{{ $matter->container_ID }}</td>
				<td>{{ $matter->parent_ID }}</td>
				<td>{{ $matter->responsible }}</td>
				<td>{{ $matter->updater }}</td>
				<td>{{ $matter->updated }}</td>
				<td>{{ $matter->expires }}</td>
				<td>{{ $matter->notes }}</td>
			</tr>
		@endforeach
		<tr><td>&nbsp;</td></tr>
	</table>
@stop
	
@section('footer')

	<div style="position: fixed; bottom: 0;">{{ $matters->links() }}</div>

@stop