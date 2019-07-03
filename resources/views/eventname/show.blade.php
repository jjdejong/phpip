<div id="edit-ename-content">
	<fieldset class="ename-info-set">
		<legend>Event name details</legend>
		<table class="table table-hover table-sm" data-id="{{ $enameInfo->code }}" data-source="/eventname/">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $enameInfo->code }}"></td>
				<td><label for="is_task" title="{{ $tableComments['is_task'] }}">Is task</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="is_task" value="1" {{ $enameInfo->is_task ? 'checked' : "" }}>
						<label class="form-check-label" for="is_task">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="is_task" value="0" {{ $enameInfo->is_task ? "" : 'checked'}}>
						<label class="form-check-label" for="is_task">No</label>
					</div>
				</td>
			</tr>
			<tr>
				<td><label for="name" title="{{ $tableComments['name'] }}">Name</label></td>
				<td><input class="form-control noformat" list="ajaxDatalist" data-ac="/event-name/autocomplete/0" name="name" value="{{ $enameInfo->name }}"></td>
				<td><label for="status_event" title="{{ $tableComments['status_event'] }}">Is status event</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="status_event" value="1" {{ $enameInfo->status_event ? 'checked' : "" }}>
						<label class="form-check-label">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="status_event" value="0" {{ $enameInfo->status_event ? "" : 'checked'}}>
						<label class="form-check-label">No</label>
					</div>
				</td>
			</tr>
			<tr>
				<td><label for="category" title="{{ $tableComments['category'] }}">Category</label></td>
				<td><input type="text" class="form-control noformat" list="ajaxDatalist" data-ac="/category/autocomplete" name="category" value="{{ empty($enameInfo->categoryInfo) ? '' : $enameInfo->categoryInfo->category }}"></td>
				<td><label for="use_matter_resp" title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="use_matter_resp" value="1" {{ $enameInfo->use_matter_resp ? 'checked' : "" }}>
						<label class="form-check-label">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="use_matter_resp" value="0" {{ $enameInfo->use_matter_resp ? "" : 'checked='}}>
						<label class="form-check-label">No</label>
					</div>
				</td>
			</tr>
			<tr>
				<td><label for="country" title="{{ $tableComments['country'] }}">Country</label></td>
				<td><input type="text" class="form-control noformat" name="country" list="ajaxDatalist" data-ac="/country/autocomplete" value="{{ empty($enameInfo->countryInfo) ? '' : $enameInfo->countryInfo->name }}"></td>
				<td><label for="unique" title="{{ $tableComments['unique'] }}">Is unique</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="unique" value="1" {{ $enameInfo->unique ? 'checked' : "" }}>
						<label class="form-check-label">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="unique" value="0" {{ $enameInfo->unique ? "" : 'checked'}}>
						<label class="form-check-label">No</label>
					</div>
				</td>
			</tr>
			<tr>
				<td><label for="default_responsible" title="{{ $tableComments['default_responsible'] }}">Default responsible</label></td>
				<td><input type="text" class="form-control noformat" list="ajaxDatalist" data-ac="/user/autocomplete" name="default_responsible" value="{{ empty($enameInfo->default_responsibleInfo) ? "" : $enameInfo->default_responsibleInfo->name }}"></td>
				<td><label for="uqtrigger" title="{{ $tableComments['uqtrigger'] }}">Unique trigger</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="uqtrigger" value="1" {{ $enameInfo->uqtrigger ? 'checked' : "" }}>
						<label class="form-check-label">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="uqtrigger" value="0" {{ $enameInfo->uqtrigger ? "" : 'checked'}}>
						<label class="form-check-label">No</label>
					</div>
				</td>
			</tr>
			<tr>
				<td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label></td>
				<td><textarea class="form-control form-control-sm noformat" name="notes">{{ $enameInfo->notes }}</textarea>
				<td><label for="killer" title="{{ $tableComments['killer'] }}">Is killer</label></td>
				<td>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="killer" value="1" {{ $enameInfo->killer ? 'checked' : "" }}>
						<label class="form-check-label">Yes</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" class="form-check-input noformat" name="killer" value="0" {{ $enameInfo->killer ? "" : 'checked'}}>
						<label class="form-check-label">No</label>
					</div>
				</td>
			</tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete event name" id="delete-ename" data-dismiss="modal" data-id="{{ $enameInfo->code }}">
			Delete
		</button>
	</fieldset>

</div>
