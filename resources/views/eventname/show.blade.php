<div id="edit-ename-content">
	<fieldset class="ename-info-set">
		<legend>Event name details - ID: {{ $enameInfo->code }}</legend>
		<table data-id="{{ $enameInfo->code }}" data-source="/eventname/">
                <tr><td><label>Code</label>
                </td><td><label>{{ $enameInfo->code }}</label>
                </td><td><label for="is_task" title="{{ $tableComments['is_task'] }}">Is task</label>
                </td><td><span class="noformat" name="is_task">
                        <input type="radio" class="noformat" name="is_task" value="1" {{ $enameInfo->is_task ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="is_task" value="0" {{ $enameInfo->is_task ? "" : 'checked="checked"'}} />No
                </span>
                </td></tr><tr><td><label for="name" title="{{ $tableComments['name'] }}">Name</label>
                </td><td><input class="form-control noformat" data-ac="/event-name/autocomplete/0" name="name" value="{{ $enameInfo->name }}">
                </td><td><label for="status_event" title="{{ $tableComments['status_event'] }}">Is status event</label>
                </td><td><span class="noformat" name="status_event">
                        <input type="radio" class="noformat" name="status_event" value="1" {{ $enameInfo->status_event ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="status_event" value="0" {{ $enameInfo->status_event ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="category" title="{{ $tableComments['category'] }}">Category</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="category" value="{{ empty($enameInfo->categoryInfo) ? '' : $enameInfo->categoryInfo->category }}">
                </td><td><label for="use_matter_resp" title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible</label>
                </td><td><span class="noformat" name="use_matter_resp">
                        <input type="radio" class="noformat" name="use_matter_resp" value="1" {{ $enameInfo->use_matter_resp ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="use_matter_resp" value="0" {{ $enameInfo->use_matter_resp ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="country" title="{{ $tableComments['country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" name="country" data-ac="/country/autocomplete" value="{{ empty($enameInfo->countryInfo) ? '' : $enameInfo->countryInfo->name }}">
                </td><td><label for="unique" title="{{ $tableComments['unique'] }}">Is unique</label>
                </td><td><span name="unique">
                        <input type="radio" class="noformat" name="unique" value="1" {{ $enameInfo->unique ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="unique" value="0" {{ $enameInfo->unique ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="default_responsible" title="{{ $tableComments['default_responsible'] }}">Default responsible</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" data-ac="/user/autocomplete" name="default_responsible" value="{{ empty($enameInfo->default_responsibleInfo) ? "" : $enameInfo->default_responsibleInfo->name }}">
                </td><td><label for="uqtrigger" title="{{ $tableComments['uqtrigger'] }}">Unique trigger</label>
                </td><td><span name="uqtrigger">
                        <input type="radio" class="noformat" name="uqtrigger" value="1" {{ $enameInfo->uqtrigger ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="uqtrigger" value="0" {{ $enameInfo->uqtrigger ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label>
                </td><td class="ui-front">
					<textarea class="form-control form-control form-control-sm noformat" cols="" rows="" name="notes">{{ $enameInfo->notes }}</textarea>
				<td><label for="killer" title="{{ $tableComments['killer'] }}">Is killer</label>
                </td><td><span name="killer">
                        <input type="radio" class="noformat" name="killer" value="1" {{ $enameInfo->killer ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" class="noformat" name="killer" value="0" {{ $enameInfo->killer ? "" : 'checked="checked"'}} />No
                </span>
                </tr>
			</table>
        <button type="button" class="btn btn-danger" title="Delete event name" id="delete-ename" data-dismiss="modal" data-id="{{ $enameInfo->code }}">
			&times; Delete
		</button>
	</fieldset>
	
</div>

