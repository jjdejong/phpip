<td colspan="8">
<form id="sendDocumentForm">
  <input type='hidden' value="{{ $matter->id }}" name="matter_id">
  @if (isset($event))
  <input type='hidden' value="{{ $event->id }}" name="event_id">
  @endif
  @if (isset($task))
  <input type='hidden' value="{{ $task->id }}" name="task_id">
  @endif
    <div class='container'>
    <div class='row font-weight-bold'>
      <div class="col-3 bg-light">
        Contact
      </div>
      <div class="col-1 bg-light">
        Send to:
      </div>
      <div class="col-1 bg-light">
        CC:
      </div>
      <div class="col-7">
      </div>
    </div>
    @foreach ($contacts as $contact)
      <div class='row' >
        <div class="col-3">
          {{ $contact->first_name}} {{ is_null($contact->name) ? $contact->display_name : $contact->name  }}
        </div>
        <div class="col-1">
          <input id="" class="contact" name="sendto-{{ $contact->actor_id }}" type="checkbox">
        </div>
        <div class="col-1">
          <input id="" class="contact" name="ccto-{{ $contact->actor_id }}" type="checkbox">
        </div>
        <div class="col-7">
        </div>
      </div>
    @endforeach
  </div>
  <div class='container' data-resource="/document/select/{{ $matter->id }}">
      <div class="row bg-light font-weight-bold">
          <div class="col-lg-2">
            Language
          </div>
          <div class="col-lg-2">
            Category
          </div>
          <div class="col-lg-4">
            Name
          </div>
          <div class="col-lg-3">
            Style
          </div>
          <div class="col-lg-1">
          </div>
      </div>
      @foreach ($members as $member)
        <div class="row reveal-hidden" data-resource="/document/mailto/{{ $member->id }}">
          <div class = "col-lg-2">
            {{ $member->language->language }}
          </div>
          <div class = "col-lg-2">
            {{ $member->class->category->category }}
          </div>

          <div class = "col-lg-4">
            {{ $member->class->name }}
          </div>

          <div class = "co-lgl-3">
            {{ $member->style->style }}
          </div>
          <div class = "col-lg-1">
            <a class="sendDocument btn btn-secondary btn-sm">Prepare</a>
          </div>
        </div>
      @endforeach
  </div>
</form>
</td>
