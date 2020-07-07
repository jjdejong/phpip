<table class="table table-striped table-sm mb-1" style="width: 100%; table-layout: fixed;">
  @foreach ($tasks as $task)
  <tr class="row">
    <td class="col text-truncate py-0">
      <a href="/matter/{{ $task->matter_id }}/{{ $isrenewals ? 'renewals' : 'tasks' }}" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" data-resource="/task/" title="All tasks">
        {{ $task->name }} {{ $task->detail }}
      </a>
    </td>
    <td class="col-2 py-0">
      <a href="/matter/{{ $task->matter_id }}">
        {{ $task->uid }}
      </a>
    </td>
    <td class="col text-truncate py-0">
      {{ $task->title ?? $task->trademark }}
    </td>
    <td class="col-2 py-0 px-2">
      {{ $task->due_date->isoFormat('L') }}
      @if ($task->due_date < now())
      <div class="badge badge-danger py-0" title="Overdue">&nbsp;</div>
      @elseif ($task->due_date < now()->addWeeks(2))
      <div class="badge badge-warning py-0" title="Urgent">&nbsp;</div>
      @endif
    </td>
    @canany(['admin', 'readwrite'])
    <td class="col-1 py-0 px-3">
      <input id="{{ $task->id }}" class="clear-open-task" type="checkbox">
    </td>
    @endcanany
  </tr>
  @endforeach
</table>
{{ $tasks->links() }}
