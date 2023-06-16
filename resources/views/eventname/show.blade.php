<div data-resource="/eventname/{{ $eventname->code }}" class="reload-part">
	<table class="table table-hover table-sm">
		<tr>
			<th width="20%">{{ _i("Code") }}</th>
			<td><input class="noformat form-control" name="code" value="{{ $eventname->code }}"></td>
			<td><input type="checkbox" class="noformat" name="is_task" {{ $eventname->is_task ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['is_task'] }}">{{ _i("Is Task") }}</label></th>
		</tr>
		<tr>
			<th>{{ _i('Name') }}</th>
			<td><input class="form-control noformat" name="name" value="{{ $eventname->name }}"></td>
			<td><input type="checkbox" class="noformat" name="status_event" {{ $eventname->status_event ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['status_event'] }}">{{ _i("Is Status") }}</label></th>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['category'] }}">{{ _i("Category") }}</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="category" value="{{ empty($eventname->categoryInfo) ? '' : $eventname->categoryInfo->category }}"></td>
			<td><input type="checkbox" class="noformat" name="killer" {{ $eventname->killer ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['killer'] }}">{{ _i('Is Killer') }}</label></th>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['country'] }}">{{ _i('Country') }}</label></th>
			<td><input type="text" class="form-control noformat" name="country" data-ac="/country/autocomplete" value="{{ empty($eventname->countryInfo) ? '' : $eventname->countryInfo->name }}"></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['default_responsible'] }}">{{ _i('Default Responsible') }}</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/user/autocomplete" name="default_responsible" value="{{ empty($eventname->default_responsibleInfo) ? "" : $eventname->default_responsibleInfo->name }}"></td>
			<td><input type="checkbox" class="noformat" name="use_matter_resp" {{ $eventname->use_matter_resp ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['use_matter_resp'] }}">{{ _i('Use Matter Responsible') }}</label></th>
		</tr>
		<tr>
			<th>{{ _i('Notes') }}</th>
			<td colspan="3"><textarea class="form-control form-control-sm noformat" name="notes">{{ $eventname->notes }}</textarea>
		</tr>
		<tr>
			<th colspan="3">{{ _i("Linked templates") }}</th>
			<td>
				<a data-toggle="collapse" class="badge badge-pill badge-info font-weight-normal ml-2" href="#addEventRow" id="addEventTempalte" title="{{ _i('Add template') }}">
					&plus;
				</a>
			</td>
		</tr>
    <tr id="addEventRow" class="collapse">
      <td colspan="4">
        <form id="addTemplateForm" class="form-inline">
          <input type="hidden" name="event_name_code" value="{{ $eventname->code }}">
          <div class="input-group">
            <input type="hidden" name="template_class_id" value="">
            <input type="text" class="form-control form-control-sm" name="className" placeholder="Class" data-ac="/template-class/autocomplete" data-actarget="template_class_id">
            <div class="input-group-append">
              <button type="button" class="btn btn-primary btn-sm" id="addEventTemplateSubmit">&check;</button>
              <button type="reset" class="btn btn-outline-primary btn-sm">&times;</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
		@foreach ($links as $link)
		<tr class="reveal-hidden" data-resource="/event-class/{{ $link->id }}">
			<td	title="{{ $link->class->description }}" colspan="3">
				{{ $link->class->name}}
			</td>
			<td>
        <a href="#" class="hidden-action text-danger" id="deleteTemplate" title="{{ _i('Delete template link') }}">&CircleTimes;</a>
      </td>

		</tr>
		@endforeach
	</table>
	<button type="button" class="btn btn-danger" title="{{ _i('Delete event name') }}" id="deleteEName" data-message="{{ _i('event name ') . $eventname->name  }}" data-url='/eventname/{{ $eventname->code }}'>
		{{ _i("Delete") }}
	</button>
</div>
