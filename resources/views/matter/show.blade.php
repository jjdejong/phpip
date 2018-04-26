@php
$titles = $matter->titles->groupBy('type_name');
$classifiers = $matter->classifiers->groupBy('type_name');
$actors = $matter->actors->groupBy('role_name');
$linkedBy = $matter->linkedBy->groupBy('type_code');
@endphp

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
		border-radius: 0;
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

<div id="matter_id" class="d-none">{{ $matter->id }}</div>

<div class="row card-deck mb-1">
	<div class="card border-primary col-3 p-0">
		<div class="card-header bg-primary text-white reveal-hidden lead p-1">
			<a class="bg-primary text-white" href="/matter?Ref={{ $matter->caseref }}" title="See family">{{ $matter->uid }}</a>
			({{ $matter->category->category }})
			<a class="bg-primary text-white float-right hidden-action" href="/matter/{{ $matter->id }}/edit" title="Advanced edit">
				&#9998;
			</a>
		</div>
		<div class="card-body p-1">
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
				<span class="float-right"><strong>Expiry:</strong> {{ $matter->expire_date }}</span>
			@endif
		</div>
	</div>

	<div class="card col-7 border-secondary p-0">
		<div id="titlePanel" class="card-body p-1">
		@foreach ( $titles as $type => $title_group )
			<div class="row">
				<div class="col-2 text-right font-weight-bold">{{ $type }}</div>
				<div class="col-10">
				@foreach ( $title_group as $title )
					@if ($title != $title_group->first()) <br> @endif
					<span id="{{ $title->id }}" class="titleItem" contenteditable="true">{{ $title->value }}</span>&nbsp;
				@endforeach
					@if ($title == $title_group->last() && $type == $titles->keys()->last())
					<a class="badge badge-pill badge-primary float-right" data-toggle="collapse" href="#addTitleForm">+</a>
					@endif
				</div>
			</div>
		@endforeach
			<div id="addTitleForm" class="collapse">
				<form>
					<div class="form-row">
						<input type="hidden" name="matter_id" value="{{ $matter->container_id or $matter->id }}" />
						<input type="hidden" name="type_code" />
						<div class="col-2">
							<input type="text" class="form-control form-control-sm" name="type" placeholder="Type" />
						</div>
						<div class="col-10">
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="value" placeholder="Value" />
								<div class="input-group-append">
									<button type="button" class="btn btn-primary btn-sm" id="addTitleSubmit">&check;</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="card border-info col-2 p-0">
		<div class="card-body">
			<button id="clone-matter-link" type="button" class="btn btn-outline-info btn-block btn-sm"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				&boxbox; Clone Matter
			</button>
			<button id="child-matter-link" type="button" class="btn btn-outline-info btn-block btn-sm"
				data-caseref="{{ $matter->caseref }}"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				&oplus;
				New Child
			</button>
			@if ( $matter->countryInfo->goesnational )
			<button id="national-matter-link"
				data-caseref="{{ $matter->caseref }}" type="button" class="btn btn-outline-info btn-block btn-sm"
				data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
				data-origin="{{ $matter->origin }}"
				data-type="{{ $matter->type_code }}"
				data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
				&#9872;
				Enter Nat. Phase
			</button>
			@endif
		</div>
	</div>
</div>

<div class="row card-deck">
	<div id="actorPanel" class="card col-3 border-secondary p-0">
		<div class="card-header reveal-hidden text-white bg-secondary font-weight-bold p-1">
			Actors
			<a id="addActorPopover"
				class="badge badge-pill badge-light hidden-action float-right"
				rel="popover"
				data-placement="right"
				href="#"
				data-html="true"
				title="Add Actor"
				data-content='<form id="addActorForm">
						<input type="hidden" name="shared" value="1" />
						<input type="hidden" name="company_id" value="" />
						<div class="ui-front">
							<input type="text" class="form-control form-control-sm" name="role" placeholder="Role" />
							<input type="text" class="form-control form-control-sm" name="actor_id" placeholder="Name" />
							<input type="text" class="form-control form-control-sm" name="actor_ref" placeholder="Reference" />
						</div>
						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="radio" id="actorShared" name="matter_id" value="{{ $matter->container_id or $matter->id }}">
								<label class="form-check-label" for="actorShared">Add to container and share</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" id="actorNotShared" name="matter_id" value="{{ $matter->id }}">
								<label class="form-check-label" for="actorNotShared">Add to this matter only (not shared)</label>
							</div>
						</div>
						<div class="btn-group" role="group">
							<button type="button" class="btn btn-info btn-sm" id="addActorSubmit">&check;</button>
							<button type="button" class="btn btn-outline-info btn-sm" id="popoverCancel">&times;</button>
						</div>
					</form>
					<div class="alert alert-danger d-none" role="alert"></div>'>
				&plus;
			</a>
		</div>
		<div class="card-body p-1">
			@foreach ( $actors as $role_name => $role_group )
				<div class="card reveal-hidden border-secondary mb-1">
					<div class="card-header font-weight-bold p-1">
						{{ $role_name }}
						<a class="hidden-action float-right ml-2"
							data-toggle="modal"
							data-target="#listModal"
							data-remote="false"
							title="Edit actors in {{ $role_group[0]->role_name }} group"
							href="/matter/{{ $matter->id }}/roleActors/{{ $role_group[0]->role_code }}"
							data-resource="/actor-pivot/">
							&#9998;
						</a>
						<a id="addActorPopover"
							class="hidden-action float-right"
							data-placement="right"
							rel="popover"
							data-html="true"
							title='Add {{ $role_name }}'
							data-content='<form id="addActorForm">
									<input type="hidden" name="role" value="{{ $role_group[0]->role_code }}" />
									<input type="hidden" name="shared" value="{{ $role_group[0]->shareable }}" />
									<input type="hidden" name="company_id" value="" />
									<div class="ui-front">
										<input type="text" class="form-control form-control-sm" name="actor_id" placeholder="Name" />
										<input type="text" class="form-control form-control-sm" name="actor_ref" placeholder="Reference" />
									</div>
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="radio" id="actorShared" name="matter_id" value="{{ $matter->container_id or $matter->id }}" {{ $role_group[0]->shareable ? "checked" : "" }}>
											<label class="form-check-label" for="actorShared">Add to container and share</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" id="actorNotShared" name="matter_id" value="{{ $matter->id }}" {{ $role_group[0]->shareable ? "" : "checked" }}>
											<label class="form-check-label" for="actorNotShared">Add to this matter only (not shared)</label>
										</div>
									</div>
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-info btn-sm" id="addActorSubmit">&check;</button>
										<button type="button" class="btn btn-outline-info btn-sm" id="popoverCancel">&times;</button>
									</div>
								</form>
								<div class="alert alert-danger d-none" role="alert"></div>'
							href="#">
							&oplus;
						</a>
					</div>
					<div class="card-body p-1" style="max-height: 80px; overflow: auto;">
						<ul class = "list-unstyled">
						@foreach ( $role_group as $actor)
							<li {!! $actor->inherited ? 'style="font-style: italic;"' : '' !!}>
								@if ( $actor->warn && $role_name == 'Client' )
									<span title="Payment Difficulties">&#9888;</span>
								@endif
								{{ $actor->display_name }}
								@if ( $actor->show_ref && $actor->actor_ref )
									({{ $actor->actor_ref }})
								@endif
								@if ( $actor->show_company && $actor->company )
									&nbsp;- {{ $actor->company }}
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
			@endforeach
		</div>
	</div>
	<div id="multiPanel" class="card col-9 p-0">
		<div class="row card-deck mb-1">
				<div class="card col-6 p-0 reveal-hidden">
					<div class="card-header p-1">
						<div class="row">
							<span class="font-weight-bold col-5">Status</span>
							<span class="col-3">Date</span>
							<span class="col-4">
								Number
								<a class="hidden-action float-right"
									href="/matter/{{ $matter->id }}/events"
									data-toggle="modal"
									data-target="#listModal"
									data-remote="false"
									title="All events"
									data-resource="/event/">
									<span class="badge badge-primary">&vellip;</span>
								</a>
							</span>
						</div>
					</div>
					<div class="card-body p-1" id="status-panel" style="overflow: auto;">
						@foreach ( $matter->events->where('info.status_event', 1) as $event )
						<div class="row">
							<span class="col-5">{{ $event->info->name }}</span>
							@if ( $event->alt_matter_id )
								<span class="col-3">{{ $event->link->event_date }}</span>
								<span class="col">
									<a href="/matter/{{ $event->alt_matter_id }}" target="_blank">{{ $event->altMatter->country . $event->link->detail }}</a>
								</span>
							@else
								<span class="col-3">{{ $event->event_date }}</span>
								<span class="col">
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
			<div class="card col-6 p-0 reveal-hidden">
				<div class="card-header p-1">
					<div class="row">
						<span class="font-weight-bold col-9">Open Tasks</span>
						<span class="col-3">
							Due
							<a class="hidden-action float-right"
								href="/matter/{{ $matter->id }}/tasks"
								data-toggle="modal"
								data-target="#listModal"
								data-remote="false"
								title="All tasks"
								data-resource="/task/">
								<span class="badge badge-primary">&vellip;</span>
							</a>
						</span>
					</div>
				</div>
				<div class="card-body p-1" id="opentask-panel" style="overflow: auto;">
					@foreach ( $matter->tasksPending as $task )
					<div class="row">
						<span class="col-9">{{ $task->info->name }}: {{ $task->detail }}</span>
						<span class="col-3">{{ $task->due_date }}</span>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div class="row card-deck mb-1">
			<div class="card col-2 p-0 reveal-hidden">
				<div class="card-header p-1">
					<div class="row">
						<span class="font-weight-bold col-6">Renewals</span>
						<span class="col-6">
							Due
							<a class="hidden-action float-right"
								href="/matter/{{ $matter->id }}/renewals"
								data-toggle="modal" data-target="#listModal"
								data-remote="false"
								title="All renewals"
								data-resource="/task/">
								<span class="badge badge-primary">&vellip;</span>
							</a>
						</span>
					</div>
				</div>
				<div class="card-body p-1" id="renewal-panel" style="overflow: auto;">
					@foreach ( $matter->renewalsPending->take(3) as $task )
					<div class="row">
						<span class="col-4">{{ $task->detail }}</span>
						<span class="col-8">{{ $task->due_date }}</span>
					</div>
					@endforeach
				</div>
			</div>
			<div class="card col-6 p-0 reveal-hidden">
				<div class="card-header font-weight-bold p-1">
					Classifiers
					<a class="hidden-action float-right"
						href="#classifiersModal"
						data-toggle="modal"
						title="Classifier detail">
						<span class="badge badge-primary">&vellip;</span>
					</a>
				</div>
				<div class="card-body p-1" id="classifier-panel" style="overflow: auto;">
					@foreach ( $classifiers as $type => $classifier_group )
					<div class="row">
						<span class="col-2"><strong>{{ $type }}</strong></span>
						<span class="col-10">
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
							<span class="col-1"><strong>Link</strong></span>
							<span class="col-11">
							@foreach ( $matter->linkedBy as $linkedBy )
								<a href="/matter/{{ $linkedBy->id }}">{{ $linkedBy->uid }}</a>
							@endforeach
							</span>
						</div>
					@endif
				</div>
			</div>
			<div class="card border-info col-4 p-0">
				<div class="card-header font-weight-bold bg-info text-white p-1">
					Related Matters
					<span class="float-right">&#9432;</span>
				</div>
				<div class="card-body p-1" id="related-panel" style="overflow: auto;">
					@if ( $matter->has('family') )
						<p>
						<strong>Fam</strong>
						@foreach ( $matter->family as $member )
							<a class="badge badge-primary" href="/matter/{{ $member->id }}">{{ $member->suffix }}</a>
						@endforeach
						</p>
					@endif
					@foreach ( $matter->priorityTo->groupBy('caseref') as $caseref => $family )
						<p>
							<strong>{{ $caseref }}</strong>
						@foreach ( $family as $rmatter )
							<a class="badge badge-primary" href="/matter/{{ $rmatter->id }}">{{ $rmatter->suffix }}</a>
						@endforeach
						</p>
					@endforeach
				</div>
			</div>
		</div>
		<div class="row card-deck">
			<div class="card col-12 p-0">
				<div class="card-header font-weight-bold p-1">
					Notes
					<button type="button" class="hidden-action btn btn-warning btn-sm" id="updateNotes">&#9432; Save</button>
				</div>
				<div class="card-body p-1" id="notes-panel" style="overflow: auto;">
					<textarea id="notes" class="form-control noformat" style="width:100%; height:100%; box-sizing: border-box;" name="notes">{{ $matter->notes }}</textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modals -->
@include('partials.matter-show-modals')

@stop

@section('script')

@include('matter.show-js')

@stop
