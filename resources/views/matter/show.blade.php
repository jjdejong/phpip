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
    background: transparent;
    color: inherit;
    padding: 0px;
    height: inherit;
    display: inline;
}
</style>

@stop

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
						<span id="titleItem" data-id="{{ $title->id }}" contenteditable="true">{{ $title->value }}</span>
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
					<span class="glyphicon glyphicon-duplicate pull-left"></span>
					Clone Matter
				</button>
				<button id="child-matter-link" type="button" class="btn btn-info btn-block"
					data-caseref="{{ $matter->caseref }}"
					data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
					data-origin="{{ $matter->origin }}"
					data-type="{{ $matter->type_code }}"
					data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
					<span class="glyphicon glyphicon-link pull-left"></span> 
					New Child
				</button>
				@if ( $matter->countryInfo->goesnational )
				<button id="national-matter-link"
					data-caseref="{{ $matter->caseref }}" type="button" class="btn btn-info btn-block"
					data-country="{{ $matter->countryInfo->name }}-{{ $matter->country }}"
					data-origin="{{ $matter->origin }}"
					data-type="{{ $matter->type_code }}"
					data-code="{{ $matter->category->category }}-{{ $matter->category_code }}">
					<span class="glyphicon glyphicon-flag pull-left"></span>
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
			<div class="panel-heading panel-title reveal-hidden">
				Actors
				<a class="hidden-action pull-right" data-toggle="modal" href="#addActor" title="Add Actor" data-role="">
					<span class="glyphicon glyphicon-plus-sign bg-primary"></span>
				</a>
			</div>
			<div class="panel-body panel-group" id="actor-panel">
				@foreach ( $matter->actors()->groupBy('role_name') as $key => $role_group )
				<div class="row">
					<div class="col-sm-12">
					<div class="panel panel-default reveal-hidden">
						<div class="panel-heading panel-title">
							<div class="row">
								<span class="col-xs-9">{{ $key }}</span>
								<a class="hidden-action col-xs-2" data-toggle="modal" href="#editRoleGroup" title="Edit group" data-role="{{ $role_group[0]->role }}">
									<span class="glyphicon glyphicon-edit text-success"></span>
								</a>
								<a class="hidden-action col-xs-1" data-toggle="modal" href="#addActor" title="Add Actor as {{ $key }}" data-role="{{ $role_group[0]->role }}">
									<span class="glyphicon glyphicon-plus-sign text-info"></span>
								</a>
							</div>
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
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-5">Status</span>
							<span class="col-xs-3">Date</span>
							<span class="col-xs-4">
								Number
								<a href="/matter/{{ $matter->id }}/events" class="hidden-action pull-right" data-toggle="modal" data-target="#allEventsModal" data-remote="false">
									<span class="glyphicon glyphicon-open bg-primary" data-toggle="tooltip" title="All events"></span>
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
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-9">Open Tasks</span>
							<span class="col-xs-3">
								Due
								<a href="/matter/{{ $matter->id }}/tasks" class="hidden-action pull-right" data-toggle="modal" data-target="#taskListModal" data-remote="false" title="All tasks">
									<span class="glyphicon glyphicon-open bg-primary"></span>
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
			<div class="col-sm-3">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						<div class="row">
							<span class="col-xs-6">Renewals</span>
							<span class="col-xs-6">
								Due
								<a href="/matter/{{ $matter->id }}/renewals" class="hidden-action pull-right" data-toggle="modal" data-target="#taskListModal" data-remote="false" title="All renewals">
									<span class="glyphicon glyphicon-open bg-primary"></span>
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
			<div class="col-sm-5">
				<div class="panel panel-primary reveal-hidden">
					<div class="panel-heading panel-title">
						Classifiers
						<a href="/matter/{{ $matter->id }}/classifiers" class="hidden-action pull-right" data-toggle="modal" data-target="#classifierDetail" data-remote="false" title="Classifier detail">
							<span class="glyphicon glyphicon-open bg-primary"></span>
						</a>
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
							<span class="glyphicon glyphicon-flash text-danger"></span>
						</a>
					</div>
					<div class="panel-body" id="notes-panel" style="height: 100px; overflow: auto;">
						<span id="notes" contenteditable="true">{!! $matter->notes or '...' !!}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modals -->

<div id="allEventsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Events</h4>
			</div>
			<div class="modal-body">
				Ajax placeholder
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

<div id="taskListModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Tasks</h4>
				<mark>Values are editable. Click on a value to change it and press Enter to save changes</mark>
			</div>
			<div class="modal-body">
				Ajax placeholder
			</div>
			<div class="modal-footer">
				<span class="alert pull-left"></span>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

<!-- <div id="addTaskToEvent" class="modal fade"> -->
<!-- 	<div class="modal-dialog modal-sm"> -->
	    <!-- Modal content-->
<!-- 	    <div class="modal-content"> -->
<!-- 		    <div class="modal-header bg-info"> -->
<!-- 				<button type="button" class="close" data-dismiss="modal">&times;</button> -->
<!-- 				<h4>New Task</h4> -->
<!-- 			</div> -->
<!-- 			<div class="modal-body bg-info"> -->
<!-- 				<form class="form-horizontal"> -->
<!-- 					<input type="hidden" name="trigger_id" value="" id="trigger_id" /> -->
<!--   					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="name">Task Name</label> -->
<!-- 						<div class="col-sm-9 ui-front"> -->
<!-- 							<input class="form-control" type="text" name="name" id="task_name" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<input type="hidden" name="code" value="" id="task_code" /> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="due_date">Due date</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="date" name="due_date" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="detail">Detail</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="text" name="detail" id="task_detail" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="cost">Cost</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="text" name="cost" id="task_cost" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="fee">Fee</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="text" name="fee" id="task_fee" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="currency">Currency</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="text" name="currency" id="task_currency" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="time_spent">Time spent</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<input class="form-control" type="text" name="time_spent" id="task_time" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="assigned_to">Assigned to</label> -->
<!-- 						<div class="col-sm-9 ui-front"> -->
<!-- 							<input class="form-control" type="text" name="assigned_to" id="task_assigned_to" value="" /> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 					<div class="form-group"> -->
<!-- 						<label class="control-label col-sm-3" for="notes">Notes</label> -->
<!-- 						<div class="col-sm-9"> -->
<!-- 							<textarea class="form-control" name="notes" id="task_notes"></textarea> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 				</form> -->
<!-- 			</div> -->
<!-- 			<div class="modal-footer bg-info"> -->
<!-- 				<button type="button" class="btn btn-primary" name="add_task_submit" id="add-task-submit">Add task</button> -->
<!-- 				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
<!-- 			</div> -->
<!-- 		</div> -->
<!-- 	</div> -->
<!-- </div> -->

@stop

@section('script')

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    $("#taskListModal, #allEventsModal").on("show.bs.modal", function(event) {
        $(this).find(".modal-body").load( $(event.relatedTarget).attr("href") );
    });

    $("#taskListModal").on("hide.bs.modal", function(event) {
        $("#opentask-panel").load("/matter/{{ $matter->id }} #opentask-panel > div");
    });

	$("#notes").keyup(function() {
		$("#updateNotes").removeClass('hidden-action');
		$(this).addClass('changed');
	});

	$("#notes").blur(function() {
		if ( $(this).hasClass('changed') ) {
			$.post("/matter/{{ $matter->id }}", 
				{ notes: $("#notes").text(), _token: "{{ csrf_token() }}", _method: "PUT" });
			$("#updateNotes").addClass('hidden-action');
			$(this).removeClass('changed');
		}
	});
});

/*$(document).on("click", "#add-task-submit", function() {
	var request = $("form").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/task', request)
	.done(function() {
		$("#addTaskToEvent").modal("hide");
		$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
	}).fail(function(errors) {
		$.each(errors.responseJSON, function (key, item) {
			$("#addTaskToEvent").find('input[name=' + key + ']').attr("placeholder", item).closest('.form-group').addClass('has-error');
		});
	});
});

$(document).on("click", "#deleteTask", function() {
	if( confirm("Do you want to delete task?") ){
		$.post('/task/' + $(this).data('id'),
			{ _token: "{{ csrf_token() }}", _method: "DELETE" }
		).done(function() {
			$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
		});
	}
});

$(document).on("click","#deleteEvent", function() {
	if ( confirm("Deleting the event will also delete the linked tasks") ) {
		$.post('/event/' + $(this).data('id'),
			{ _token: "{{ csrf_token() }}", _method: "DELETE" },
			function() {
				$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
			}
		);
	}
});*/
</script>

@stop