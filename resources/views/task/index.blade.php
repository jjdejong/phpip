<table class="table table-striped table-sm">
  @foreach ($tasks as $task)
  <tr>
    <td class="row py-0">
      <div class="col-6">
        <a href="/matter/{{ $task->matter_id }}/tasks" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" data-resource="/task/" title="All tasks">
          {{ $task->name }}{{ $task->detail ? " - ".$task->detail : "" }}
        </a>
      </div>
      <div class="col-3">
        <a href="/matter/{{ $task->matter_id }}">
          {{ $task->uid }}
        </a>
      </div>
      @if ($task->due_date < now())
      <div class="col-2 text-danger">
      @elseif ($task->due_date < now()->addWeek())
      <div class="col-2" style="color: purple;">
      @else
      <div class="col-2">
      @endif
        {{ $task->due_date->isoFormat('L') }}
      </div>
      <div class="col-1 px-4">
        <input id="{{ $task->id }}" class="clear-open-task" type="checkbox">
      </div>
    </td>
  </tr>
  @endforeach
</table>
