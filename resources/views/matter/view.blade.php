@extends('layouts.app')

@section('content')

<?php
if ( $matter->container_id )
	$classifiers = $matter->container->classifiers;
else
	$classifiers = $matter->classifiers;
$titles = $classifiers->where('type.main_display', 1)->sortBy('type.display_order')->groupBy('type.type');
$classifiers = $classifiers->where('type.main_display', 0)->sortBy('type.display_order')->groupBy('type.type');
$linkedBy = $matter->linkedBy->groupBy('type_code');
?>

<div class="row">
	<div class="col-sm-3">
	<div class="panel panel-primary" style="min-height: 96px">
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
	<div class="panel panel-primary" style="min-height: 96px">
		<div class="panel-body">
		@foreach ( $titles as $key => $title_group )
			<div class="row">
				<span class="col-xs-2"><strong>{{ $key }}:</strong></span>
				<span class="col-xs-10">
				@foreach ( $title_group as $title )
					{{ $title->value }}
				@endforeach
				</span>
			</div>
		@endforeach
		</div>
	</div>
	</div>
	<div class="col-sm-2">
	<div class="panel panel-primary" style="min-height: 96px">
		<div class="panel-body">
			<button id="clone-matter-link" type="button" class="btn btn-info btn-block"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				<span class="glyphicon glyphicon-duplicate" style="float: left;"></span>
				Clone Matter
			</button>
			<button id="child-matter-link" type="button" class="btn btn-info btn-block"
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
				data-caseref="{{ $matter->caseref }}" type="button" class="btn btn-info btn-block"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				<span class="glyphicon glyphicon-flag" style="float: left;"></span>
				Enter Nat. Phase
			</button>
			@endif
		</div>
	</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
	<div class="panel panel-primary">
		<div class="panel-heading panel-title">Actors</div>
		<div class="panel-body panel-group" id="actor-panel">
			@foreach ( $matter->actors()->groupBy('role_name') as $key => $role_group )
			<div class="col-sm-3">
			<div class="panel panel-default">
				<div class="panel-heading panel-title">
					{{ $key }}<span class="glyphicon glyphicon-plus pull-right" data-role="{{ $role_group[0]->role }}"></span>
				</div>
				<div class="panel-body" style="height: 72px; overflow: auto;">
					<ul class = "list-unstyled">
					@foreach ( $role_group as $actor)
						<li {!! $actor->inherited ? 'style="font-style: italic;"' : '' !!}>
							{{ $actor->name }}
							@if ( $actor->show_ref && $actor->actor_ref )
								({{ $actor->actor_ref }})
							@endif
							@if ( $actor->show_company && $actor->company_id )
								&nbsp;- {{ App\Actor::find($actor->company_id)->name }}
							@endif
							@if ( $actor->show_date && $actor->date )
								({{ $actor->date }})
							@endif
							@if ( $actor->show_rate && $actor->rate )
								&nbsp;- {{ $actor->rate }}
							@endif
						</li>
					@endforeach
					</ul>
				</div>
			</div>
			</div>
			@endforeach
		</div>
	</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
	<div class="panel panel-primary">
		<div class="panel-heading panel-title">
			<div class="row">
				<span class="col-xs-4">Status</span>
				<span class="col-xs-3">Date</span>
				<span class="col-xs-5">Number<span class="glyphicon glyphicon-open pull-right"></span></span>
			</div>
		</div>
		<div class="panel-body" id="status-panel" style="height: 100px; overflow: auto;">
			@foreach ( $matter->events->where('info.status_event', 1) as $event )
			<div class="row">
				<span class="col-xs-4">{{ $event->info->name }}</span>
				@if ( $event->alt_matter_id )
					<span class="col-xs-3">{{ $event->link->event_date }}</span>
					<span class="col-xs-5">
						<a href="/matter/{{ $event->alt_matter_id }}">{{ $event->link->matter->country }}{{ $event->link->detail }}</a>
					</span>
				@else
					<span class="col-xs-3">{{ $event->event_date }}</span>
					<span class="col-xs-5">{{ $event->detail }}</span>
				@endif
			</div>
			@endforeach
		</div>
	</div>
	</div>
	<div class="col-sm-6">
	<div class="panel panel-primary">
		<div class="panel-heading panel-title">
			<div class="row">
				<span class="col-xs-9">Open Tasks</span>
				<span class="col-xs-3">Due<span class="glyphicon glyphicon-open pull-right"></span></span>
			</div>
		</div>
		<div class="panel-body" id="opentask-panel" style="height: 100px; overflow: auto;">
			@foreach ( $matter->tasksPending as $task )
			<div class="row">
				<span class="col-xs-9">{{ $task->info->name }}: {{ $task->detail }}</span>
				<span class="col-xs-3">{{ $task->due_date }}</span>
			</div>
			@endforeach
		</div>
	</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
	<div class="panel panel-primary">
		<div class="panel-heading panel-title">
			<div class="row">
				<span class="col-xs-6">Renewals</span>
				<span class="col-xs-6">Due<span class="glyphicon glyphicon-open pull-right"></span></span>
			</div>
		</div>
		<div class="panel-body" id="renewal-panel" style="height: 100px; overflow: auto;">
			@foreach ( $matter->renewalsPending->take(3) as $task )
			<div class="row">
				<span class="col-xs-6">{{ $task->detail }}</span>
				<span class="col-xs-6">{{ $task->due_date }}</span>
			</div>
			@endforeach
		</div>
	</div>
	</div>
	<div class="col-sm-5">
	<div class="panel panel-primary">
		<div class="panel-heading panel-title">
			Classifiers
		</div>
		<div class="panel-body" id="classifier-panel" style="height: 100px; overflow: auto;">
		@foreach ( $classifiers as $key => $classifier_group )
			<div class="row">
				<span class="col-xs-1"><strong>{{ $key }}:</strong></span>
				<span class="col-xs-11">
				@foreach ( $classifier_group as $classifier )
					@if ( $classifier->url )
						<a href="{{ $classifier->url }}" target="_blank">{{ $classifier->value }}</a>
					@elseif ( $classifier->lnk_matter_id )
						<a href="/matter/{{ $classifier->lnk_matter_id }}">{{ $classifier->linkedMatter->uid }}</a>
					@else
						{{ $classifier->value }}
					@endif
				@endforeach
				</span>
			</div>
		@endforeach	
		</div>
	</div>
	</div>
	<div class="col-sm-4">
	<div class="panel panel-default">
		<div class="panel-heading panel-title">
			Notes
		</div>
		<div class="panel-body" id="notes-panel" style="height: 100px; overflow: auto;">
			{{ $matter->notes }}
		</div>
	</div>
	</div>
</div>
@stop

@section('script')

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

@stop