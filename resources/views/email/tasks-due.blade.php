<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title></title>
  </head>
  <body>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>Ref</th>
        <th>Cat</th>
        <th>Task</th>
        <th>Due</th>
        <th>Resp.</th>
      </tr>
      @foreach($tasks as $task)
      <tr>
        <td><a href="{{ $phpip_url }}/{{ $task->matter->id }}">{{ $task->matter->uid }}</a></td>
        <td>{{ $task->matter->category_code }}</td>
        <td>{{ "{$task->info->name}{$task->detail ? " - $task->detail" : ''}" }}</td>
        <td>{{ $task->due_date->isoFormat('L') }}</td>
        <td>{{ $task->matter->responsible }}</td>
      </tr>
      @endforeach
    </table>
  </body>
</html>
