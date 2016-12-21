@extends('layouts.app')

@section('content')

	@foreach ($matters as $matter)
	<span>
		{{ $matter->caseref . $matter->country }}
	</span>
	@endforeach

@stop