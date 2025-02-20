@inject('sharePoint', 'App\Services\SharePointService')

<table class="table table-hover table-sm">
  <thead class="table-light">
    <tr>
      <th>
        Event
        @can('readwrite')
        <a data-bs-toggle="collapse" class="text-info ms-2" href="#addEventRow" id="addEvent" title="Add event">
          <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#plus-circle-fill"/></svg>
        </a>
        @endcan
      </th>
      <th>Date</th>
      <th>Number</th>
      <th>Notes</th>
      <th>Refers to</th>
      @can('readonly')
      <th>Email</th>
      @endcan
    </tr>
    <tr id="addEventRow" class="collapse">
      <td colspan="5">
        <form id="addEventForm">
          <input type="hidden" name="matter_id" value="{{ $matter->id }}">
          <div class="input-group">
            <input type="hidden" name="code">
            <input type="text" class="form-control form-control-sm" name="eventName" placeholder="Event" data-ac="/event-name/autocomplete/0?category={{ $matter->category_code }}" data-actarget="code">
            <input type="text" class="form-control form-control-sm" name="event_date" placeholder="Date (xx/xx/yyyy)">
            <input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail">
            <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes">
            <input type="hidden" name="alt_matter_id">
            <input type="text" class="form-control form-control-sm"  placeholder="Refers to" data-ac="/matter/autocomplete" data-actarget="alt_matter_id">
            <button type="button" class="btn btn-primary btn-sm" id="addEventSubmit">&check;</button>
            <button type="reset" class="btn btn-outline-primary btn-sm">&times;</button>
          </div>
        </form>
      </td>
    </tr>
  </thead>
  <tbody id="eventList">
    @foreach ( $events as $event )
    <tr data-resource="/event/{{ $event->id }}">
      <td>
        @php
            $sharePointLink = null;
            if ($sharePoint->isEnabled() && 
                array_key_exists($event->code, config('services.sharepoint.event_codes'))) {
                $sharePointLink = $sharePoint->findFolderLink(
                    $matter->caseref,
                    $matter->suffix,
                    config('services.sharepoint.event_codes')[$event->code] . $event->detail
                );
            }
        @endphp
        @if($sharePointLink)
            <a href="{{ $sharePointLink }}" target="_blank">
                {{ $event->info->name }}
            </a>
        @else
            {{ $event->info->name }}
        @endif
      </td>
      <td><input type="text" class="form-control noformat" name="event_date" value="{{ $event->event_date->isoFormat('L') }}"></td>
      <td><input type="text" class="form-control noformat" size="16" name="detail" value="{{ $event->detail }}"></td>
      <td><input type="text" class="form-control noformat" name="notes" value="{{ $event->notes }}"></td>
      <td><input type="text" class="form-control noformat" size="10" name="alt_matter_id" data-ac="/matter/autocomplete" value="{{ $event->altMatter ? $event->altMatter->uid : '' }}"></td>
      @can('readonly')
      <td>
            @if (count(App\Models\EventName::where('code',$event->code)->first()->templates) != 0)
            <button class="chooseTemplate button btn-info" data-url="/document/select/{{ $matter->id }}?EventName={{ $event->code }}&Event={{ $event->id }}" >&#9993;</button>
            @endif
      </td>
      @endcan
    </tr>
    @endforeach
  </tbody>
</table>
<a class="badge text-bg-primary float-end" href="https://github.com/jjdejong/phpip/wiki/Events,-Deadlines-and-Tasks#events" target="_blank">?</a>
<div id="templateSelect">
</div>
