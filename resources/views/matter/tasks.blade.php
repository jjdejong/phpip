<script>
$(document).ready(function() {

	$('input[type="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		onSelect: function(date, instance) {
			$(this).focus();
			$(this).parent("td").addClass("bg-warning");
		}
	});
	
	$('input.noformat').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			var data = $.param({ _token: "{{ csrf_token() }}", _method: "PUT" }) + "&" + $(this).serialize();
			$.post('/task/'+ $(this).closest("tr").data("task_id"), data)
			.done(function () {
				$("#taskListModal").find(".modal-body").load("/matter/{{ $events[0]->matter_id }}/tasks");
				$("#taskListModal").find(".alert").removeClass("alert-danger").html("");
			}).fail(function(errors) {
				$.each(errors.responseJSON, function (key, item) {
					$("#taskListModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
				});
			});
		}
		$(this).parent("td").addClass("bg-warning");   
	});

	$('input[type="checkbox"]').click(function() {
		var flag = 0;
		if ( $(this).is(":checked") ) flag = 1;
		$.post('/task/'+ $(this).closest("tr").data("task_id"), { _token: "{{ csrf_token() }}", _method: "PUT", done: flag })
		.done(function () {
			$("#taskListModal").find(".modal-body").load("/matter/{{ $events[0]->matter_id }}/tasks");
			$("#taskListModal").find(".alert").removeClass("alert-danger").html("");
		})
	});
	
	$('input[name="assigned_to"]').autocomplete({
		minLength: 2,
		source: "/user/search",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
			if ($(this).hasClass("noformat")) $(this).parent().addClass("alert alert-warning");
		}
	});
});
</script>

<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th>Event/Tasks</th>
			<th>Due date</th>
			<th>Done</th> 
			<th>Done date</th> 
			<th>Cost</th> 
			<th>Fee</th>
			<th>Cur.</th>
			@if (true)
				<th>Time</th>
			@endif
			<th>Assigned To</th> 
			<th>Notes</th>
			<th style="width: 24px;">&nbsp;</th>
		</tr>
	</thead>
	@foreach ( $events as $event )
	<tbody>
		<tr class="reveal-hidden">
			<td colspan="3">
				<span style="position: relative; left: -10px; margin-right: 10px;" class="text-warning"><strong>{{ $event->info->name . ": " . $event->event_date }}</strong></span>
				<a href="javascript:void(0);" id="addTaskToEvent" class="hidden-action" data-id="{{ $event->id }}" title="Add task to {{ $event->info->name }}">
					<span class="glyphicon glyphicon-plus-sign"></span>
				</a>
				<a href="javascript:void(0);" class="hidden-action" id="deleteEvent" data-id="{{ $event->id }}" title="Delete event" style="margin-left: 15px;">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
			<td colspan="8"></td>
		</tr>
		@foreach ($event->tasks as $task)
		<tr class="reveal-hidden text-{{ $task->done ? 'success' : 'danger' }}" data-task_id="{{ $task->id }}">
			<td nowrap>
				{{ $task->info->name }} <input type="text" class="form-control noformat" name="detail" value="{{ $task->detail }}"/>
			</td> 
			<td>
				<input type="date" class="form-control noformat" size="10" name="due_date" value="{{ $task->due_date }}" data-rule_id="{{ $task->rule_used }}"/>
			</td>
			<td>
				<input type="checkbox" name="done" {{ $task->done ? 'checked' : '' }}>
			</td> 
			<td>
				<input type="date" class="form-control noformat" size="10" name="done_date" value="{{ $task->done_date }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" size="6" name="cost" value="{{ $task->cost }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" size="6" name="fee" value="{{ $task->fee }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" size="3" name="currency" value="{{ $task->currency }}"/>
			</td>
			@if (true)
			<td>
				<input type="time" class="form-control noformat" size="6" name="time_spent" value="{{ $task->time_spent }}"/>
			</td>
			@endif
			<td class="ui-front">
				<input type="text" class="form-control noformat" size="12" name="assigned_to" value="{{ $task->assigned_to }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" name="notes" value="{{ $task->notes }}"/>
			</td>
			<td>
				<a href="javascript:void(0);" class="hidden-action" id="deleteTask" data-id="{{ $task->id }}" title="Delete task">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
		</tr>
		@endforeach
	</tbody>
	@endforeach
</table>

<template id="addTaskForm">
	<form class="form-inline">
			<input type="hidden" name="trigger_id" value="" id="trigger_id" />
			<input type="hidden" name="code" value="" id="task_code" />
		<div class="form-group form-group-sm ui-front">
			<input type="text" class="form-control" name="name" placeholder="Name"/>
		</div>
		<div class="form-group form-group-sm">
			<input type="text" class="form-control" name="detail" placeholder="Detail"/>
		</div>
		<div class="form-group form-group-sm ui-front">
			<input type="date" class="form-control" size="10" name="due_date" placeholder="Date"/>
		</div>
		<div class="form-group form-group-sm">
			<input type="text" class="form-control" size="6" name="cost" placeholder="Cost"/>
		</div>
		<div class="form-group form-group-sm">
			<input type="text" class="form-control" size="6" name="fee" placeholder="Fee"/>
		</div>
		<div class="form-group form-group-sm ui-front">
			<input type="text" class="form-control" size="3" name="currency" placeholder="EUR"/>
			<input type="time" class="form-control" size="6" name="time_spent" placeholder="Time"/>
			<input type="text" class="form-control" size="12" name="assigned_to" placeholder="Assigned to"/>
			<input type="text" class="form-control" name="notes" placeholder="Notes"/>
			<button type="button" class="btn btn-primary" id="addTaskSubmit"><span class="glyphicon glyphicon-ok"></span></button>
			<button type="button" class="btn btn-primary" id="addTaskCancel"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</form>
</template>
