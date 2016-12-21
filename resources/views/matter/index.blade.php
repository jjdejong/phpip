@extends('layouts.app')

@section('content')

	<table class="table table-striped table-hover table-condensed">
		@foreach ($matters as $matter)
			@if ($matter->container_ID)
				<tr>
			@else
				<tr class="info"> 
			@endif
				<td>{{ $matter->caseref . $matter->country }}</td>
				<td>{{ $matter->category }}</td>
				<td>{{ $matter->notes }}</td>
			</tr>
		@endforeach
	</table>

@stop