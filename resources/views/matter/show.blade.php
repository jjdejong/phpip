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
    background: transparent;
    color: inherit;
    padding: 0px;
    height: inherit;
    display: inline;
}
</style>

@stop

@section('content')

<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-primary" style="min-height: 96px">
			<div class="panel-heading panel-title">
				<a href="/matter?Ref={{ $matter->caseref }}" data-toggle="tooltip" data-placement="right" title="See family">{{ $matter->uid }}</a>
				({{ $matter->category->category }})
				<a href="/matter/{{ $matter->id }}/edit">
					<i class="glyphicon glyphicon-edit pull-right" data-toggle="tooltip" data-placement="right" title="Avanced edit"></i>
				</a>
			</div>
			<div class="panel-body">
				<ul class="list-unstyled">
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
										<i class="glyphicon glyphicon-exclamation-sign text-danger" data-toggle="tooltip" title="Payment Difficulties"></i>
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
								<a href="/matter/{{ $matter->id }}/events" class="hidden-action pull-right" data-toggle="modal" data-target="#allEventsModal" data-remote="false">
									<i class="glyphicon glyphicon-list bg-primary" data-toggle="tooltip" title="All events"></i>
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
								<a href="/matter/{{ $matter->id }}/tasks" class="hidden-action pull-right" data-toggle="modal" data-target="#taskListModal" data-remote="false" title="All tasks">
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
								<a href="/matter/{{ $matter->id }}/renewals" class="hidden-action pull-right" data-toggle="modal" data-target="#taskListModal" data-remote="false" data-renewals="1" title="All renewals">
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
						<a href="#classifiersModal" class="hidden-action pull-right" data-toggle="modal" title="Classifier detail">
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
						<a href="javascript:void(0);" class="hidden-action" id="updateNotes" title="Update notes">
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

<div id="allEventsModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
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
				<span class="alert pull-left"></span>
				<mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
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
				<h4>Tasks per event</h4>
			</div>
			<div class="modal-body">
				Ajax placeholder
			</div>
			<div class="modal-footer">
				<span class="alert pull-left"></span>
				<mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

<div id="classifiersModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Classifier Detail</h4>
			</div>
			<div class="modal-body">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Type/Value</th>
							<th>URL</th>
							<th>Link to matter</th>
							<th>
								<a href="javascript:void(0);" id="addClassifier">
									<span class="glyphicon glyphicon-plus-sign pull-right" title="Add classifier"></span>
								</a>
							</th>
						</tr>
					</thead>
					@foreach ($classifiers as $type => $classifier_group)
						<tbody>
						<tr class="reveal-hidden">
							<td class="text-warning">{{ $type }}</td>
							<td colspan="3">
								<a href="javascript:void(0);" id="addClassifierToType" class="hidden-action" data-type="{{ $classifier_group[0]->type_code }}">
									<span class="glyphicon glyphicon-plus-sign" title="Add classifier"></span>
								</a>
								<a href="javascript:void(0);" id="deleteClassifierGroup" class="hidden-action" data-type="{{ $classifier_group[0]->type_code }}">
									<span class="glyphicon glyphicon-trash text-danger" title="Delete classifier group"></span>
								</a>
							</td>
						</tr>
						</tbody>
						<tbody class="sortable">
						@foreach($classifier_group as $classifier)
							<tr class="reveal-hidden" data-id="{{ $classifier->id }}">
								<td><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}"/></td>
								<td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}"/></td>
								<td><input type="text" class="form-control noformat" name="lnk_matter_id" value="{{ $classifier->lnkTo }}"></td>
								<td>
									<input type="hidden" name="display_order" value="{{ $classifier->display_order }}"/>
									<a href="javascript:void(0);" class="hidden-action" id="deleteClassifier" data-id="{{ $classifier->id }}" title="Delete classifier">
										<span class="glyphicon glyphicon-trash text-danger"></span>
									</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					@endforeach
				</table>
			</div>
			<div class="modal-footer">
				<span class="alert pull-left"></span>
				<mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

@stop

@section('script')

<script>
var tasksOrRenewals = 'tasks'; // Identifies what to display in the tasks modal. Set through the data-renewals attribute of the button for opening the renewals panel

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    if ({{ sizeof($titles) }} == 0)
        $("#addTitleForm").collapse("show");
    
	// Ajax fill the opened modal
    $("#taskListModal, #allEventsModal").on("show.bs.modal", function(event) {
        $(this).find(".modal-body").load( $(event.relatedTarget).attr("href") );
        // Are we calling the tasks panel or renewals panel?
		if ( $(event.relatedTarget).data("renewals") ) tasksOrRenewals = 'renewals';
		else tasksOrRenewals = 'tasks';
    });

	// Ajax refresh the status and task-dependant panels when the tasks or events modal is closed
    $("#taskListModal, #allEventsModal").on("hide.bs.modal", function(event) {
        $("#multiPanel").load("/matter/{{ $matter->id }} #multiPanel > div");
    });

	$("#notes").keyup(function() {
		$("#updateNotes").removeClass('hidden-action');
		$(this).addClass('changed');
	});

	$("#updateNotes").click(function() {
		if ( $("#notes").hasClass('changed') ) {
			$.post("/matter/{{ $matter->id }}", 
				{ _token: "{{ csrf_token() }}", _method: "PUT", notes: $("#notes").val() });
			$("#updateNotes").addClass('hidden-action');
			$(this).removeClass('changed');
		}
	});
});

$("#titlePanel").on("keypress", ".titleItem", function (e) {
	if (e.which == 13) {
		e.preventDefault();
		$.post('/classifier/' + $(this).attr("id"), 
			{ _token: "{{ csrf_token() }}", _method: "PUT", value: $(this).text() }
		).done(function() {
			$(".titleItem").removeClass("bg-warning");
		});
	}
	$(this).addClass("bg-warning");   
});

$("#titlePanel").on("shown.bs.collapse", "#addTitleForm", function() {
   	$(this).find('input[name="type"]').focus().autocomplete({
		minLength: 0,
		source: "/classifier-type/autocomplete?main_display=1",
		select: function( event, ui ) {
			$("#addTitleForm").find('input[name="type_code"]').val( ui.item.id );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
});

$("#titlePanel").on("click", "#addTitleSubmit", function() {
	var request = $("#addTitleForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/classifier', request)
	.done(function() {
		$('#titlePanel').load("/matter/{{ $matter->id }} #titlePanel > div");
	}).fail(function(errors) {
		$.each(errors.responseJSON, function (key, item) {
			$("#addTitleForm").find('input[name=' + key + ']').attr("placeholder", item).parent().addClass("has-error");
		});
	});
});

$("#taskListModal").on("click", "#addTaskToEvent", function() {
	$(this).parents("tbody").append( $("#addTaskFormTemplate").html() );
   	$("#addTaskForm").find('input[name="trigger_id"]').val( $(this).data("id") );
   	$("#addTaskForm").find('input[name="name"]').autocomplete({
		minLength: 2,
		source: "/event-name/autocomplete?is_task=1",
		select: function( event, ui ) {
			$("#addTaskForm").find('input[name="code"]').val( ui.item.id );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addTaskForm").find('input[name="assigned_to"]').autocomplete({
		minLength: 2,
		source: "/user/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addTaskForm").find('input[type="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
	});
});

$("#taskListModal").on("click", "#addTaskSubmit", function() {
	var request = $("#addTaskForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/task', request)
	.done(function() {
		$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
	}).fail(function(errors) {
		$.each(errors.responseJSON, function (key, item) {
			$("#addTaskForm").find('input[name=' + key + ']').attr("placeholder", item).parent().addClass("has-error");
		});
	});
});

$("#taskListModal").on("click", "#addTaskCancel", function() {
	$("#addTaskCancel").parents("tr").html("");
});

$("#taskListModal").on("click", "#deleteTask", function() {
	if( confirm("Do you want to delete task?") ){
		$.post('/task/' + $(this).data('id'),
			{ _token: "{{ csrf_token() }}", _method: "DELETE" }
		).done(function() {
			$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/" + tasksOrRenewals);
		});
	}
});

$("#taskListModal").on("click","#deleteEvent", function() {
	if ( confirm("Deleting the event will also delete the linked tasks") ) {
		$.post('/event/' + $(this).data('id'),
			{ _token: "{{ csrf_token() }}", _method: "DELETE" },
			function() {
				$('#taskListModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
			}
		);
	}
});

$("#allEventsModal").on("click", "#addEvent", function() {
	$("tbody").append( $("#addEventFormTemplate").html() );
   	$("#addEventForm").find('input[name="name"]').autocomplete({
		minLength: 2,
		source: "/event-name/autocomplete",
		select: function( event, ui ) {
			$("#addEventForm").find('input[name="code"]').val( ui.item.id );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addEventForm").find('input[name="alt_matter_id"]').autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addEventForm").find('input[type="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
	});
});

$("#allEventsModal").on("click", "#addEventSubmit", function() {
	var request = $("#addEventForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/event', request)
	.done(function() {
		$('#allEventsModal').find(".modal-body").load("/matter/{{ $matter->id }}/events");
	}).fail(function(errors) {
		$.each(errors.responseJSON, function (key, item) {
			$("#addEventForm").find('input[name=' + key + ']').attr("placeholder", item).parent().addClass("has-error");
		});
	});
});

$("#allEventsModal").on("click", "#addEventCancel", function() {
	$("#addEventCancel").parents("tr").html("");
});
</script>

@stop