<?php
if ( $matter->container_id )
	$classifiers = $matter->container->classifiers;
else
	$classifiers = $matter->classifiers;
$titles = $classifiers->where('type.main_display', 1)->sortBy('type.display_order')->groupBy('type.type');
$classifiers = $classifiers->where('type.main_display', 0)->sortBy('type.display_order')->groupBy('type.type');
$linkedBy = $matter->linkedBy->groupBy('type_code');
?>

@extends('layouts.app')

@section('style')

<style>
.hidden-action {
	display: none;
}
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

<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-primary" style="min-height: 96px">
			<div class="panel-heading panel-title">
				<a href="/matter?Ref={{ $matter->caseref }}" title="See family">{{ $matter->uid }}</a>
				({{ $matter->category->category }})
				<a href="/matter/{{ $matter->id }}/edit" title="Advanced edit">
					<i class="glyphicon glyphicon-edit pull-right"></i>
				</a>
			</div>
			<div class="panel-body">
				<ul class="list-unstyled">
					@if ($matter->container_id)
					<li><a href="/matter/{{ $matter->container_id }}" title="See container">
						{{ $matter->container->uid }}
					</a></li>
					@endif
					@if ($matter->parent_id)
					<li><a href="/matter/{{ $matter->parent_id }}" title="See parent">
						{{ $matter->parent->uid }}
					</a></li>
				@endif
				</ul>
				@if ($matter->expire_date)
					<span class="pull-right"><strong>Expiry:</strong> {{ $matter->expire_date }}</span>
				@endif
			</div>
		</div>
	</div>
	<div class="col-sm-7">
		<div class="panel panel-primary" style="min-height: 96px">
			<div id="titlePanel" class="panel-body">
			@foreach ( $titles as $type => $title_group )
				<div class="row">
					<div class="col-xs-2"><strong class="pull-right">{{ $type }}</strong></div>
					<div class="col-xs-10">
					@foreach ( $title_group as $title )
						@if ($title != $title_group->first()) <br> @endif
						<span id="{{ $title->id }}" class="titleItem" contenteditable="true">{{ $title->value }}</span>&nbsp;
					@endforeach
						@if ($title == $title_group->last()  && $type == $titles->keys()->last())
						<a data-toggle="collapse" href="#addTitleForm">
							<i class="glyphicon glyphicon-plus-sign text-info pull-right"></i>
						</a>
						@endif
					</div>
				</div>
			@endforeach
				<div id="addTitleForm" class="row collapse">
					<form class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="matter_id" value="{{ $matter->container_id or $matter->id }}" />
						<input type="hidden" name="type_code" />
						<div class="col-xs-2">
							<div class="input-group">
								<input type="text" class="form-control" name="type" placeholder="Type" />
							</div>
						</div>
						<div class="col-xs-10">
							<div class="input-group">
								<input type="text" class="form-control" name="value" placeholder="Value" />
								<div class="input-group-btn">
									<button type="button" class="btn btn-primary" id="addTitleSubmit"><i class="glyphicon glyphicon-ok"></i></button>
								</div>
							</div>
						</div>
					</form>
				</div>
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
					<i class="glyphicon glyphicon-duplicate pull-left"></i>
					Clone Matter
				</button>
				<button id="child-matter-link" type="button" class="btn btn-info btn-block"
					data-caseref="{{ $matter->caseref }}"
					data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
					data-origin="{{ $matter->origin }}"
					data-type="{{ $matter->type_code }}"
					data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
					<i class="glyphicon glyphicon-link pull-left"></i> 
					New Child
				</button>
				@if ( $matter->countryInfo->goesnational )
				<button id="national-matter-link"
					data-caseref="{{ $matter->caseref }}" type="button" class="btn btn-info btn-block"
					data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
					data-origin="{{ $matter->origin }}"
					data-type="{{ $matter->type_code }}"
					data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
					<i class="glyphicon glyphicon-flag pull-left"></i>
					Enter Nat. Phase
				</button>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-primary" style="min-height: 410px">
			<div class="panel-heading panel-title reveal-hidden">
				Actors
				<a class="hidden-action pull-right" data-toggle="modal" href="#addActor" title="Add Actor" data-role="">
					<i class="glyphicon glyphicon-plus-sign bg-primary"></i>
				</a>
			</div>
			<div class="panel-body panel-group" id="actor-panel">
				@foreach ( $matter->actors()->groupBy('role_name') as $role_name => $role_group )
				<div class="row">
					<div class="col-sm-12">
					<div class="panel panel-default reveal-hidden">
						<div class="panel-heading panel-title">
							<div class="row">
								<span class="col-xs-9">{{ $role_name }}</span>
								<a class="hidden-action col-xs-2" data-toggle="modal" href="#editRoleGroup" title="Edit group" data-role="{{ $role_group[0]->role }}">
									<i class="glyphicon glyphicon-edit text-success"></i>
								</a>
								<a class="hidden-action col-xs-1" data-toggle="modal" href="#addActor" title="Add Actor as {{ $role_name }}" data-role="{{ $role_group[0]->role }}">
									<i class="glyphicon glyphicon-plus-sign text-info"></i>
								</a>
							</div>
						</div>
						<div class="panel-body" style="max-height: 80px; overflow: auto;">
							<ul class = "list-unstyled">
							@foreach ( $role_group as $actor)
								<li {!! $actor->inherited ? 'style="font-style: italic;"' : '' !!}>
									@if ( $actor->warn && $role_name == 'Client' )
										<i class="glyphicon glyphicon-exclamation-sign text-danger" title="Payment Difficulties"></i>
									@endif
									{{ $actor->name }}
									@if ( $actor->show_ref && $actor->actor_ref )
										({{ $actor->actor_ref }})
									@endif
									@if ( $actor->show_company && $actor->company_id )
										&nbsp;- {{ App\Actor::find($actor->company_id)->display_name }}
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
				</div>
				@endforeach
			</div>
		</div>
	</div>

	<div id="multiPanel" class="col-sm-9">
		<div class="row">
			<div class="col-sm-6">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-5">Status</span>
							<span class="col-xs-3">Date</span>
							<span class="col-xs-4">
								Number
								<a href="/matter/{{ $matter->id }}/events" class="hidden-action pull-right" data-toggle="modal" data-target="#listModal" data-remote="false" title="All events" data-resource="/event/">
									<i class="glyphicon glyphicon-list bg-primary"></i>
								</a>
							</span>
						</div>
					</div>
					<div class="panel-body" id="status-panel" style="height: 100px; overflow: auto;">
						@foreach ( $matter->events->where('info.status_event', 1) as $event )
						<div class="row">
							<span class="col-xs-5">{{ $event->info->name }}</span>
							@if ( $event->alt_matter_id )
								<span class="col-xs-3">{{ $event->link->event_date }}</span>
								<span class="col-xs-4">
									<a href="/matter/{{ $event->alt_matter_id }}" target="_blank">{{ $event->altMatter->country . $event->link->detail }}</a>
								</span>
							@else
								<span class="col-xs-3">{{ $event->event_date }}</span>
								<span class="col-xs-4">
								@if ( $event->publicUrl() )
									<a href="{{ $event->publicUrl() }}" target="_blank">{{ $event->detail }}</a>
								@else
									{{ $event->detail }}
								@endif
								</span>
							@endif
						</div>
						@endforeach
					</div>
				</div>	
			</div>
			<div class="col-sm-6">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-9">Open Tasks</span>
							<span class="col-xs-3">
								Due
								<a href="/matter/{{ $matter->id }}/tasks" class="hidden-action pull-right" data-toggle="modal" data-target="#listModal" data-remote="false" title="All tasks" data-resource="/task/">
									<i class="glyphicon glyphicon-list bg-primary"></i>
								</a>
							</span>
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
			<div class="col-sm-2">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-6">Renewals</span>
							<span class="col-xs-6">
								Due
								<a href="/matter/{{ $matter->id }}/renewals" class="hidden-action pull-right" data-toggle="modal" data-target="#listModal" data-remote="false" title="All renewals"  data-resource="/task/">
									<i class="glyphicon glyphicon-list bg-primary"></i>
								</a>
							</span>
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
			<div class="col-sm-6">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						Classifiers
						<a href="#classifiersModal" class="hidden-action pull-right" data-toggle="modal" title="Classifier detail" data-resource="/classifier/">
							<i class="glyphicon glyphicon-list bg-primary"></i>
						</a>
					</div>
					<div class="panel-body" id="classifier-panel" style="height: 100px; overflow: auto;">
						@foreach ( $classifiers as $type => $classifier_group )
						<div class="row">
							<span class="col-xs-2"><strong>{{ $type }}</strong></span>
							<span class="col-xs-10">
							@foreach ( $classifier_group as $classifier )
								@if ( $classifier->url )
									<a href="{{ $classifier->url }}" target="_blank">{{ $classifier->value }}</a>
								@elseif ( $classifier->lnk_matter_id )
									<a href="/matter/{{ $classifier->lnk_matter_id }}">{{ $classifier->linkedMatter->uid }}</a>
								@else
									{{ $classifier->value }}
								@endif
							@endforeach
							@if ( $type == 'Link' )
								@foreach ( $matter->linkedBy as $linkedBy )
									<a href="/matter/{{ $linkedBy->id }}">{{ $linkedBy->uid }}</a>
								@endforeach
							@endif
							</span>
						</div>
						@endforeach
						@if ( !in_array('Link', $classifiers->keys()->all()) && !$matter->linkedBy->isEmpty() )
							<div class="row">
								<span class="col-xs-1"><strong>Link</strong></span>
								<span class="col-xs-11">
								@foreach ( $matter->linkedBy as $linkedBy )
									<a href="/matter/{{ $linkedBy->id }}">{{ $linkedBy->uid }}</a>
								@endforeach
								</span>
							</div>
						@endif
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-info">
					<div class="panel-heading panel-title">
						Related Matters
					</div>
					<div class="panel-body" id="related-panel" style="height: 100px; overflow: auto;">
						<div class="row">
						@if ( $matter->has('family') )
							<strong>{{ $matter->caseref }}</strong>
						@endif
						@foreach ( $matter->family as $member )
							<a href="/matter/{{ $member->id }}">{{ $member->suffix }}</a>
						@endforeach
						</div>
						@foreach ( $matter->priorityTo->groupBy('caseref') as $caseref => $family )
							<div class="row">
								<strong>{{ $caseref }}</strong>
							@foreach ( $family as $rmatter )
								<a href="/matter/{{ $rmatter->id }}">{{ $rmatter->suffix }}</a>
							@endforeach
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading panel-title">
						Notes
						<a href="#" class="hidden-action" id="updateNotes" title="Update notes">
							<i class="glyphicon glyphicon-save text-danger"></i>
						</a>
					</div>
					<div class="panel-body" id="notes-panel" style="height: 100px; overflow: auto;">
						<textarea id="notes" class="form-control noformat" style="width:100%; height:100%; box-sizing: border-box;" name="notes">{{ $matter->notes }}</textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modals -->
@include('partials.matter-show-modals')

@stop

@section('script')

<script src="{{ asset('js/matter-show.js') }}"></script>

@stop