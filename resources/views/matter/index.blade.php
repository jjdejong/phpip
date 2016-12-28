@extends('layouts.app')

@section('content')

	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>Ref</th>
			<th>Cat</th>
			<th>Status</th>
			<th>Status_date</th>
			<th>Client</th>
			<th>ClRef</th>
			<th>Agent</th>
			<th>AgtRef</th>
			<th>Title</th>
			<th>Inventor1</th>
		</tr>
		<tr>
			<td><input class="form-control input-sm" placeholder="{{ $matters->sort_id }}"></td>
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
				<td>{{ $matter->Ref }}</td>
				<td>{{ $matter->Cat }}</td>
				<td>{{ $matter->Status }}</td>
				<td>{{ $matter->Status_date }}</td>
				<td>{{ $matter->Client }}</td>
				<td>{{ $matter->ClRef }}</td>
				<td>{{ $matter->Agent }}</td>
				<td>{{ $matter->AgtRef }}</td>
				<td>{{ $matter->Title }}</td>
				<td>{{ $matter->Inventor1 }}</td>
			</tr>
		@endforeach
		<tr><td>&nbsp;</td></tr>
	</table>
@stop
	
@section('footer')

	<div style="position: fixed; bottom: 0;">{{ $matters->links() }}</div>

@stop