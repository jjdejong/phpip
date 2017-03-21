<style>
.hidden-actions {
	display: none;
}
.reveal-hidden:hover .hidden-actions {
	display: inline-block;
}
</style>

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
			<td>
				<span style="position: relative; left: -10px;" class="bg-info">{{ $event->info->name . ": " . $event->event_date }}</span>
				<a href="#addTaskToEvent" class="hidden-actions pull-right" data-toggle="modal" id="{{ $event->id }}" title="Add task to {{ $event->info->name }}">
					<span class="glyphicon glyphicon-plus-sign"></span>
				</a>
			</td>
			<td>
				<a href="/event/{{ $event->id }}/delete" class="hidden-actions" data-toggle="modal" data-target="#deleteEvent" data-remote="false" id="{{ $event->id }}" title="Delete event">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
			<td colspan="9"></td>
		</tr>
		@foreach ($event->tasks as $task)
		<tr class="reveal-hidden text-{{ $task->done ? 'success' : 'danger' }}" id="<?=$task->id?>">
			<td class="editable" title="Click to edit detail">
				<?=$task->info->name?>
				<?=$task->detail != "" ? ": " . $task->detail : ''?>
			</td> 
			<td id="<?=$task->id?>" class="editable" data-rule_id="<?=$task->rule_used?>">
				<?=$task->due_date?>
			</td>
			<td>
				<input type="checkbox" name="doneflag-<?=$task->id?>" value="<?=$task->id?>"
				<?=$task->done ? 'checked' : '' ?>>
			</td> 
			<td id="<?=$task->id?>" class="editable">
				<?=$task->done_date?>
			</td>
			<td class="editable" title="Click to edit cost">
				<?=$task->cost?>
			</td>
			<td class="editable" title="Click to edit fee">
				<?=$task->fee?>
			</td>
			<td class="editable" title="Click to edit">
				<?=$task->currency?>
			</td>
			@if (true)
			<td class="editable" title="Click to edit (HH:MM:SS)">
				<?=$task->time_spent?>
			</td>
			@endif
			<td class="edit-assigned-to" title="Click to edit">
				<?=$task->assigned_to?>
			</td>
			<td class="editable" title="Click to edit notes">
				<?=$task->task_notes?>
			</td>
			<td>
				<a href="/task/{{ $task->id }}/delete" class="hidden-actions" data-toggle="modal" data-target="#deleteTask" data-remote="false" id="{{ $task->id }}" title="Delete task">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
		</tr>
		@endforeach
	@endforeach
	</tbody>
</table>
