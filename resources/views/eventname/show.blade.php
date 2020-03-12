<div data-resource="/eventname/{{ $eventname->code }}">
	<table class="table table-hover table-sm">
		<tr>
			<th width="20%">Code</th>
			<td><input class="noformat form-control" name="code" value="{{ $eventname->code }}"></td>
			<td><input type="checkbox" class="noformat" name="is_task" {{ $eventname->is_task ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['is_task'] }}">Is Task</label></th>
		</tr>
		<tr>
			<th>Name</th>
			<td><input class="form-control noformat" name="name" value="{{ $eventname->name }}"></td>
			<td><input type="checkbox" class="noformat" name="status_event" {{ $eventname->status_event ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['status_event'] }}">Is Status</label></th>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['category'] }}">Category</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="category" value="{{ empty($eventname->categoryInfo) ? '' : $eventname->categoryInfo->category }}"></td>
			<td><input type="checkbox" class="noformat" name="killer" {{ $eventname->killer ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['killer'] }}">Is Killer</label></th>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['country'] }}">Country</label></th>
			<td><input type="text" class="form-control noformat" name="country" data-ac="/country/autocomplete" value="{{ empty($eventname->countryInfo) ? '' : $eventname->countryInfo->name }}"></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['default_responsible'] }}">Default Responsible</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/user/autocomplete" name="default_responsible" value="{{ empty($eventname->default_responsibleInfo) ? "" : $eventname->default_responsibleInfo->name }}"></td>
			<td><input type="checkbox" class="noformat" name="use_matter_resp" {{ $eventname->use_matter_resp ? 'checked' : '' }}></td>
			<th><label title="{{ $tableComments['use_matter_resp'] }}">Use Matter Responsible</label></th>
		</tr>
		<tr>
			<th>Notes</th>
			<td colspan="3"><textarea class="form-control form-control-sm noformat" name="notes">{{ $eventname->notes }}</textarea>
		</tr>
	</table>
	<button type="button" class="btn btn-danger" title="Delete event name" id="deleteEName" data-message="event name {{ $eventname->name  }}" data-url='/eventname/{{ $eventname->code }}'>
		Delete
	</button>
</div>
