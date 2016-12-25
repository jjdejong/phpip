@extends('layouts.app')

@section('content')

@can('view', $matter)
	{{ Auth::user()->role }} can view this
@endcan

	<span>
		{{ $matter->caseref . $matter->country }}
	</span>

@stop