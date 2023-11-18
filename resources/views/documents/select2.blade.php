<td colspan="8">
  <form id="sendDocumentForm" class="alert alert-info">
    <input type='hidden' value="{{ $matter->id }}" name="matter_id">
    @if(isset($event))
      <input type='hidden' value="{{ $event->id }}" name="event_id">
    @endif
    @if(isset($task))
      <input type='hidden' value="{{ $task->id }}" name="task_id">
    @endif
    <table>
      <tr>
        <th>Correspondent</th>
        <th class="text-center">To</th>
        <th class="text-center">CC</th>
      </tr>
      @foreach($contacts as $contact)
        <tr>
          <td>
            {{ $contact->display_name ?? $contact->name }} {{ $contact->first_name }}
          </td>
          <td>
            <input name="sendto-{{ $contact->actor_id }}" type="checkbox">
          </td>
          <td>
            <input name="ccto-{{ $contact->actor_id }}" type="checkbox">
          </td>
        </tr>
      @endforeach
    </table>
    <ul class="list-group" data-resource="/document/select/{{ $matter->id }}">
      @foreach($members as $member)
        <li class="list-group-item d-flex justify-content-between" data-resource="/document/mailto/{{ $member->id }}">
            <div>{{ $member->summary }} ({{ $member->language }})</div>
            <em>{{ $member->category ?? 'no category' }}</em>
            <em>{{ $member->style ?? 'no style'}}</em>
            <a class="sendDocument text-primary" href="#">
              <svg width="16" height="16" fill="currentColor" style="pointer-events: none">
                <use xlink:href="#envelope-fill" /></svg>
            </a>
        </li>
      @endforeach
    </ul>
  </form>
</td>