<table class="table table-striped table-sm" style="width: 100%; table-layout: fixed;">
  @foreach ($tasks as $task)
    @php
      if ($task->due_date < now()) {
        $warn = 'table-danger';
      } elseif ($task->due_date < now()->addWeeks(2)) {
        $warn = 'table-warning';
      } else {
        $warn = '';
      }
    @endphp
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
      <td class="col-2 py-0 px-2 {{ $warn }}">
        {{ Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}
      </td>
      @canany(['admin', 'readwrite'])
      <td class="col-1 py-0 px-4">
        <input id="{{ $task->id }}" class="clear-open-task" type="checkbox">
      </td>
      @endcanany
  </tr>
  @endforeach
</table>
