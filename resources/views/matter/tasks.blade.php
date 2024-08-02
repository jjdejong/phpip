@php 
  $ncols = 7; 
@endphp
<table class="table table-hover table-sm">
  <thead class="table-light">
    <tr>
      <th>Tasks by event</th>
      <th>Due date</th>
      <th>Ack</th>
      <th>Date</th>
      @can('readonly')
      @if($is_renewals)
      @php $ncols += 3; @endphp
      <th>Cost</th>
      <th>Fee</th>
      <th>Cur.</th>
      {{-- <th>Time</th> --}}
      @endif
      @endcan
      <th>By</th>
      <th>Notes</th>
      <th style="width: 24px;">&nbsp;</th>
      @can('readonly')
      <th>Email</th>
      @endcan
    </tr>
  </thead>
  @foreach ( $events as $event )
  <tbody>
    <tr class="reveal-hidden">
      <td colspan="{{ $ncols }}">
        <ul class="list-inline my-1">
          <li class="list-inline-item">{{ $event->info->name }}</li>
          <li class="list-inline-item">{{ $event->event_date->isoFormat('L') }}</li>
          @can('readwrite')
          <span class="hidden-action float-end">
            <li class="list-inline-item">
              <a href="#" class="text-primary" id="addTaskToEvent" data-event_id="{{ $event->id }}" title="Add task to {{ $event->info->name }}">
                <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#plus-circle-fill"/></svg>
              </a>
            </li>
            <li class="list-inline-item">
              <a href="#" class="text-danger" id="deleteEvent" data-event_id="{{ $event->id }}" title="Delete event (with tasks)">
                <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#trash-fill"/></svg>
              </a>
            </li>
            <li class="list-inline-item" style="font-size:1rem">
              <a href="#" class="text-secondary" id="regenerateTasks" data-event_id="{{ $event->id }}" title="Regenerate Tasks">
                <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#arrow-repeat"/></svg>
              </a>
            </li>
          </span>
          @endcan
        </ul>
      </td>
      @can('readonly')
      <td class="text-center align-middle">
        @if (count(App\Models\EventName::where('code', $event->code)->first()->templates) != 0)
          <a href="#" class="chooseTemplate text-info" data-url="/document/select/{{ $matter->id }}?EventName={{ $event->code }}&Event={{ $event->id }}">
            <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#envelope"/></svg>
          </a>
        @endif
      </td>
      @endcan
    </tr>
  
    @foreach ($event->tasks as $task)
    <tr class="reveal-hidden" data-resource="/task/{{ $task->id }}">
      <td nowrap>
        <span class="ms-2">{{ $task->info->name }}</span>
        <span data-name="detail" contenteditable>{{ $task->detail ?? '--' }}</span>
      </td>
      <td><input type="text" class="form-control noformat  {{ $task->done ? 'text-success' : 'text-danger' }}" name="due_date" value="{{ $task->due_date->isoFormat('L') }}"></td>
      <td><input type="checkbox" class="noformat" name="done" {{ $task->done ? 'checked' : '' }}></td>
      <td><input type="text" class="form-control noformat text-success" name="done_date" value="{{ empty($task->done_date) ? '' : $task->done_date->isoFormat('L') }}"></td>
      @can('readonly')
      @if($is_renewals)
      <td><input type="text" class="form-control noformat" name="cost" value="{{ $task->cost }}"></td>
      <td><input type="text" class="form-control noformat" name="fee" value="{{ $task->fee }}"></td>
      <td><input type="text" class="form-control noformat" name="currency" value="{{ $task->currency }}"></td>
      {{-- <td><input type="text" class="form-control noformat" name="time_spent" value="{{ $task->time_spent }}"></td> --}}
      @endif
      @endcan
      <td><input type="text" class="form-control noformat" name="assigned_to" data-ac="/user/autocomplete" value="{{ $task->assigned_to }}"></td>
      <td><input type="text" class="form-control noformat" name="notes" value="{{ $task->notes }}"></td>
      <td>
        @can('readwrite')
        <a href="#" class="hidden-action text-danger" id="deleteTask" title="Delete task">
          <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#trash"/></svg>
        </a>
        @endcan
      </td>
      @can('readonly')
      <td>
          @if (count(App\Models\EventName::where('code',$task->code)->first()->templates) != 0)
            <a href="#" class="chooseTemplate text-info fw-bold" data-url="/document/select/{{ $matter->id }}?EventName={{ $task->code }}&Task={{ $task->id }}">@</a>
          @endif
      </td>
      @endcan
    </tr>
    @endforeach
  </tbody>
  @endforeach
</table>
<a class="float-end" href="https://github.com/jjdejong/phpip/wiki/Events,-Deadlines-and-Tasks" target="_blank">
  <svg width="16" height="16" fill="currentColor" style="pointer-events: none"><use xlink:href="#question-circle-fill"/></svg>
</a>

<template id="addTaskFormTemplate">
  <tr>
    <td colspan="{{ $ncols + 1 }}">
      <form id="addTaskForm">
        <input type="hidden" name="trigger_id">
        <div class="input-group">
          <input type="hidden" name="code">
          <input type="text" class="form-control form-control-sm" placeholder="Task" data-ac="/event-name/autocomplete/1?category={{ $matter->category_code }}" data-actarget="code">
          <input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail">
          <input type="text" class="form-control form-control-sm" placeholder="Due date (xx/xx/yyyy)" name="due_date">
          <input type="hidden" name="assigned_to">
          <input type="text" class="form-control form-control-sm" placeholder="Assigned to" data-ac="/user/autocomplete" data-actarget="assigned_to">
          <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes">
          <button type="button" class="btn btn-primary btn-sm" id="addTaskSubmit">&check;</button>
          <button type="reset" class="btn btn-outline-primary btn-sm" id="addTaskReset">&times;</button>
        </div>
      </form>
    </td>
  </tr>
</template>
