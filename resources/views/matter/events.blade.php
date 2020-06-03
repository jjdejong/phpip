<table class="table table-hover table-sm">
  <thead class="thead-light">
    <tr>
      <th>
        Event
        @canany(['admin', 'readwrite'])
        <a data-toggle="collapse" class="badge badge-pill badge-info font-weight-normal ml-2" href="#addEventRow" id="addEvent" title="Add event">
          &plus;
        </a>
        @endcanany
      </th>
      <th>Date</th>
      <th>Number</th>
      <th>Notes</th>
      <th>Refers to</th>
      @cannot('client')
      <th>Email</th>
      @endcannot
    </tr>
    <tr id="addEventRow" class="collapse">
      <td colspan="5">
        <form id="addEventForm" class="form-inline">
          <input type="hidden" name="matter_id" value="{{ $matter->id }}">
          <div class="input-group">
            <input type="hidden" name="code">
            <input type="text" class="form-control form-control-sm" name="eventName" placeholder="Event" data-ac="/event-name/autocomplete/0?category={{ $matter->category_code }}" data-actarget="code">
            <input type="text" class="form-control form-control-sm" name="event_date" placeholder="Date (xx/xx/yyyy)">
            <input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail">
            <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes">
            <input type="hidden" name="alt_matter_id">
            <input type="text" class="form-control form-control-sm"  placeholder="Refers to" data-ac="/matter/autocomplete" data-actarget="alt_matter_id">
            <div class="input-group-append">
              <button type="button" class="btn btn-primary btn-sm" id="addEventSubmit">&check;</button>
              <button type="reset" class="btn btn-outline-primary btn-sm">&times;</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
  </thead>
  <tbody id="eventList">
    @foreach ( $events as $event )
    <tr data-resource="/event/{{ $event->id }}">
      <td>{{ $event->info->name }}</td>
      <td><input type="text" class="form-control noformat" name="event_date" value="{{ $event->event_date->isoFormat('L') }}"></td>
      <td><input type="text" class="form-control noformat" size="16" name="detail" value="{{ $event->detail }}"></td>
      <td><input type="text" class="form-control noformat" name="notes" value="{{ $event->notes }}"></td>
      <td><input type="text" class="form-control noformat" size="10" name="alt_matter_id" data-ac="/matter/autocomplete" value="{{ $event->altMatter ? $event->altMatter->uid : '' }}"></td>
      @cannot('client')
      <td>
            @if (count(App\EventName::where('code',$event->code)->first()->templates) != 0)
            <button class="chooseTemplate button btn-info" data-url="/document/select/{{ $matter->id }}?EventName={{ $event->code }}&Event={{ $event->id }}" >&#9993;</button>
            @endif
      </td>
      @endcannot
    </tr>
    @endforeach
  </tbody>
</table>
<div id="templateSelect">
</div>
