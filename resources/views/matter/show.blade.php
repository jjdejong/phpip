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
				<a href="/matter?Ref={{ $matter->caseref }}" data-toggle="tooltip" data-placement="right" title="See family">{{ $matter->caseref . $matter->suffix }}</a>
				({{ $matter->category->category }})
				<a href="/matter/{{ $matter->id }}/edit">
				<span class="glyphicon glyphicon-edit pull-right" data-toggle="tooltip" data-placement="right" title="Avanced edit"></span>
				</a>
			</div>
			<div class="panel-body">
				<ul>
					@if ($matter->container_id)
					<li><a href="/matter/{{ $matter->container_id }}" data-toggle="tooltip" data-placement="right" title="See container">
						{{ $matter->container->caseref . $matter->container->suffix }}
					</a></li>
					@endif
					@if ($matter->parent_id)
					<li><a href="/matter/{{ $matter->parent_id }}" data-toggle="tooltip" data-placement="right" title="See parent">
						{{ $matter->parent->caseref . $matter->parent->suffix }}
					</a></li>
				@endif
				</ul>
				<span class="pull-right"><strong>Expiry:</strong> {{ $matter->expire_date }}</span>
			</div>
		</div>
	</div>
	<div class="col-sm-7">
		<div class="panel panel-primary" style="min-height: 96px">
			<div class="panel-body">
			@foreach ( $titles as $key => $title_group )
				<div class="row">
					<span class="col-xs-2"><strong>{{ $key }}</strong></span>
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
	<div class="col-sm-3">
		<div class="panel panel-primary">
			<div class="panel-heading panel-title">Actors</div>
			<div class="panel-body panel-group" id="actor-panel">
				@foreach ( $matter->actors()->groupBy('role_name') as $key => $role_group )
				<div class="row">
					<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading panel-title">
							{{ $key }}
							<a class="pull-right" data-toggle="modal" href="#addActor" title="Add Actor" data-role="{{ $role_group[0]->role }}">
								<span class="glyphicon glyphicon-plus-sign"></span>
							</a>
						</div>
						<div class="panel-body" style="max-height: 80px; overflow: auto;">
							<ul class = "list-unstyled">
							@foreach ( $role_group as $actor)
								<li {!! $actor->inherited ? 'style="font-style: italic;"' : '' !!}>
									@if ( $actor->warn && $key == 'Client' )
										<span class="glyphicon glyphicon-exclamation-sign text-danger" data-toggle="tooltip" title="Payment Difficulties"></span>
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

	<div class="col-sm-9">
		<div class="row">
			<div class="col-sm-6">
				<div class="panel panel-primary">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-5">Status</span>
							<span class="col-xs-3">Date</span>
							<span class="col-xs-4">Number<span class="glyphicon glyphicon-open pull-right"></span></span>
						</div>
					</div>
					<div class="panel-body" id="status-panel" style="height: 100px; overflow: auto;">
						@foreach ( $matter->events->where('info.status_event', 1) as $event )
						<div class="row">
							<span class="col-xs-5">{{ $event->info->name }}</span>
							@if ( $event->alt_matter_id )
								<span class="col-xs-3">{{ $event->link->event_date }}</span>
								<span class="col-xs-4">
									<a href="/matter/{{ $event->alt_matter_id }}" target="_blank">{{ $event->link->matter->country . $event->link->detail }}</a>
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
							<span class="col-xs-6">
								Due
								<a class="pull-right" data-toggle="modal" href="#allRenewals" title="All renewals">
									<span class="glyphicon glyphicon-open" style="color: white;"></span>
								</a>
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
							<span class="col-xs-1"><strong>{{ $key }}</strong></span>
							<span class="col-xs-11">
							@foreach ( $classifier_group as $classifier )
								@if ( $classifier->url )
									<a href="{{ $classifier->url }}" target="_blank">{{ $classifier->value }}</a>
								@elseif ( $classifier->lnk_matter_id )
									<a href="/matter/{{ $classifier->lnk_matter_id }}">{{ $classifier->linkedMatter->caseref . $classifier->linkedMatter->suffix }}</a>
								@else
									{{ $classifier->value }}
								@endif
							@endforeach
							@if ( $key == 'Link' )
								@foreach ( $matter->linkedBy as $linkedBy )
									<a href="/matter/{{ $linkedBy->id }}">{{ $linkedBy->caseref . $linkedBy->suffix }}</a>
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
									<a href="/matter/{{ $linkedBy->id }}">{{ $linkedBy->caseref . $linkedBy->suffix }}</a>
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
					<div class="panel-body" id="notes-panel" style="height: 100px; overflow: auto;">
						<div class="row">
						@if ( $matter->has('family') )
							<strong>{{ $matter->caseref }}</strong>
						@endif
						@foreach ( $matter->family as $member )
							<a href="/matter/{{ $member->id }}">{{ $member->suffix }}</a>
						@endforeach
						</div>
						@foreach ( $matter->priorityTo->sortBy('caseref')->groupBy('caseref') as $caseref => $family )
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
					</div>
					<div class="panel-body" id="notes-panel" style="height: 100px; overflow: auto;">
						{{ $matter->notes }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modals -->

<div id="allRenewals" class="modal fade" role="dialog">
	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Renewals</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<span class="col-xs-6">Renewals</span>
					<span class="col-xs-6">Due</span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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