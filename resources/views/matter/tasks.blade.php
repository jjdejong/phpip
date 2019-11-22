<table class="table table-hover table-sm">
  <thead class="thead-light">
    <tr>
      <th>Tasks by event</th>
      <th>Due date</th>
      <th>OK</th>
      <th>Date</th>
      {{-- <th>Cost</th>
      <th>Fee</th>
      <th>Cur.</th>
      <th>Time</th> --}}
      <th>By</th>
      <th>Notes</th>
      <th style="width: 24px;">&nbsp;</th>
    </tr>
  </thead>
  @foreach ( $events as $event )
  <tbody>
    <tr class="reveal-hidden">
      <td colspan="12">
        <span style="position: relative; margin-right: 10px;">{{ $event->info->name . ": " . $event->event_date }}</span>
        <a href="#" id="addTaskToEvent" class="hidden-action" data-event_id="{{ $event->id }}" title="Add task to {{ $event->info->name }}">
          &CirclePlus;
        </a>
        <a href="#" class="hidden-action text-danger ml-2" id="deleteEvent" data-event_id="{{ $event->id }}" title="Delete event">
          &CircleTimes;
        </a>
      </td>
    </tr>
    @foreach ($event->tasks as $task)
    <tr class="reveal-hidden {{ $task->done ? 'text-success' : 'text-danger' }}" data-resource="/task/{{ $task->id }}">
      <td nowrap>
        <span class="ml-2">{{ $task->info->name }}</span>
        <input type="text" class="noformat" name="detail" value="{{ $task->detail }}">
      </td>
      <td><input type="date" class="form-control noformat" name="due_date" value="{{ $task->due_date }}"></td>
      <td><input type="checkbox" class="form-control noformat" name="done" {{ $task->done ? 'checked' : '' }}></td>
      <td><input type="date" class="form-control noformat" name="done_date" value="{{ $task->done_date }}"></td>
      {{-- <td><input type="text" class="form-control noformat" size="6" name="cost" value="{{ $task->cost }}"></td>
      <td><input type="text" class="form-control noformat" size="6" name="fee" value="{{ $task->fee }}"></td>
      <td><input type="text" class="form-control noformat" size="3" name="currency" value="{{ $task->currency }}"></td>
      <td><input type="text" class="form-control noformat" size="6" name="time_spent" value="{{ $task->time_spent }}"></td> --}}
      <td><input type="text" class="form-control noformat" name="assigned_to" data-ac="/user/autocomplete" value="{{ $task->assigned_to }}"></td>
      <td><input type="text" class="form-control noformat" name="notes" value="{{ $task->notes }}"></td>
      <td><a href="javascript:void(0);" class="hidden-action text-danger" id="deleteTask" title="Delete task">&CircleTimes;</a></td>
    </tr>
    @endforeach
  </tbody>
  @endforeach
</table>

<template id="addTaskFormTemplate">
  <tr>
    <td colspan="11">
      <form id="addTaskForm" class="form-inline">
        <input type="hidden" name="trigger_id">
        <div class="input-group">
          <input type="hidden" name="code">
          <input type="text" class="form-control form-control-sm" placeholder="Task" data-ac="/event-name/autocomplete/1" data-actarget="code">
          <input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail">
          <input type="date" class="form-control form-control-sm" name="due_date">
          <input type="hidden" name="assigned_to">
          <input type="text" class="form-control form-control-sm" placeholder="Assigned to" data-ac="/user/autocomplete" data-actarget="assigned_to">
          <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes">
          <div class="input-group-append">
            <button type="button" class="btn btn-primary btn-sm" id="addTaskSubmit">&check;</button>
            <button type="reset" class="btn btn-outline-primary btn-sm" onClick="$(this).parents('tr').html('')">&times;</button>
          </div>
        </div>
      </form>
    </td>
  </tr>
</template>
