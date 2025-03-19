<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title></title>
  </head>
  <body>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>{{ __('Ref') }}</th>
        <th>{{ __('Cat') }}</th>
        <th>{{ __('Client') }}</th>
        <th>{{ __('Task') }}</th>
        <th>{{ __('Due date') }}</th>
        <th>{{ __('Responsible') }}</th>
      </tr>
      @foreach($tasks as $task)
      <tr>
        <td><a href="{{ $phpip_url }}/{{ $task->matter->id }}">{{ $task->matter->uid }}</a></td>
        <td>{{ $task->matter->category_code }}</td>
        <td>{{ $task->matter->client->name }}</td>
        <td>{{ $task->info->name . ($task->detail ? " - $task->detail" : '') }}</td>
        <td>{{ $task->due_date->isoFormat('L') }}</td>
        <td>{{ $task->matter->responsible }}</td>
      </tr>
      @endforeach
    </table>
  </body>
</html>
