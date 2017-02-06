@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-primary" style="min-height: 120px;">
			<div class="panel-heading panel-title">
				<a href="/matter?Ref={{ $matter->caseref }}" data-toggle="tooltip" data-placement="right" title="See family">{{ $matter->uid }}</a>
				({{ $matter->category->category }})
				<a href="/matter/{{ $matter->id }}/edit">
				<span class="glyphicon glyphicon-edit pull-right" data-toggle="tooltip" data-placement="right" title="Avanced edit"></span>
				</a>
			</div>
			<div class="panel-body">
				<ul>
					@if ($matter->container_id)
					<li><a href="/matter/{{ $matter->container_id }}" data-toggle="tooltip" data-placement="right" title="See container">
						{{ $matter->container->uid }}
					</a></li>
					@endif
					@if ($matter->parent_id)
					<li><a href="/matter/{{ $matter->parent_id }}" data-toggle="tooltip" data-placement="right" title="See parent">
						{{ $matter->parent->uid }}
					</a></li>
				@endif
				</ul>
			</div>
		</div>
	</div>
	<div class="col-sm-7">
		<div class="panel panel-primary" style="min-height: 120px;"><div class="panel-body">
		Test
		</div></div>
	</div>
	<div class="col-sm-2">
		<div class="panel panel-primary" style="min-height: 120px;"><div class="panel-body">
			<button id="clone-matter-link" type="button" class="btn btn-primary btn-block"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				<span class="glyphicon glyphicon-duplicate" style="float: left;"></span>
				Clone Matter
			</button>
			<button id="child-matter-link" type="button" class="btn btn-primary btn-block"
				data-caseref="{{ $matter->caseref }}"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				<span class="glyphicon glyphicon-link" style="float: left;"></span> 
				New Child
			</button>
			@if ( $matter->countryInfo->goesnational )
			<button id="national-matter-link"
				data-caseref="{{ $matter->caseref }}" type="button" class="btn btn-primary btn-block"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				<span class="glyphicon glyphicon-flag" style="float: left;"></span>
				Enter Nat. Phase
			</button>
			@endif
		</div></div>
	</div>
</div>

<div class="row">

</div>

@stop

@section('script')

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

@stop