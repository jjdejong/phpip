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

	$("#task_name").autocomplete({
		minLength: 2,
		source: "/event-name/search?is_task=1",
		select: function( event, ui ) {
			$( "#task_code" ).val( ui.item.id );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
	
	$('#task_assigned_to, input[name="assigned_to"]').autocomplete({
		minLength: 2,
		source: "/user/search",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
			$(this).parent().addClass("alert alert-warning");
		}
	});
	
	$("#addTaskToEvent").on("show.bs.modal", function(event) {
	   	$("#trigger_id").val( $(event.relatedTarget).data("id") );
		$(this).find("h4").html( $(event.relatedTarget).attr("title") );
	});

	$("#addTaskToEvent").on("shown.bs.modal", function(event) {
		$("#task_name").focus();
	});

	$("#addTaskToEvent").on("hide.bs.modal", function(event) {
		$(this).find("input").val(""); // Empty input fields when modal is closed
		$(this).find(".has-error").removeClass("has-error");
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
	<tbody>
	@foreach ( $events as $event )
		<tr class="reveal-hidden">
			<td colspan="3">
				<span style="position: relative; left: -10px; margin-right: 10px;" class="text-warning"><strong>{{ $event->info->name . ": " . $event->event_date }}</strong></span>
				<a href="#addTaskToEvent" class="hidden-action" data-toggle="modal" data-id="{{ $event->id }}" title="Add task to {{ $event->info->name }}">
					<span class="glyphicon glyphicon-plus-sign"></span>
				</a>
				<a href="#" class="hidden-action" id="deleteEvent" data-id="{{ $event->id }}" title="Delete event" style="margin-left: 15px;">
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
				<a href="#" class="hidden-action" id="deleteTask" data-id="{{ $task->id }}" title="Delete task">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
		</tr>
		@endforeach
	@endforeach
	</tbody>
</table>
