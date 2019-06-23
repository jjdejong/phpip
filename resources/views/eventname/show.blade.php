<div id="edit-ename-content">
	<fieldset class="ename-info-set">
		<legend>Event name details - ID: {{ $enameInfo->code }}</legend>
		<table data-id="{{ $enameInfo->code }}" data-source="/eventname/">
                <tr><td>Code
                </td><td>{{ $enameInfo->code }}
                </td><td><label for="is_task" title="{{ $tableComments['is_task'] }}">Is task</label>
                </td><td><span class="ename-input-narrow" name="is_task">
                        <input type="radio" name="is_task" id="is_task" value="1" {{ $enameInfo->is_task ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="is_task" id="is_task" value="0" {{ $enameInfo->is_task ? "" : 'checked="checked"'}} />No
                </span>
                </td></tr><tr><td title="{{ $tableComments['name'] }}">Name
                </td><td><input class="form-control noformat" data-ac="/event-name/autocomplete/0" name="name" value="{{ $enameInfo->name }}">
                </td><td><label for="status_event" title="{{ $tableComments['status_event'] }}">Is status event</label>
                </td><td><span class="ename-input-narrow" name="status_event">
                        <input type="radio" name="status_event" id="status_event" value="1" {{ $enameInfo->status_event ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="status_event" id="status_event" value="0" {{ $enameInfo->status_event ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td title="{{ $tableComments['category'] }}">Category
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="category" value="{{ empty($enameInfo->categoryInfo) ? '' : $enameInfo->categoryInfo->category }}">
                </td><td title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible
                </td><td><span class="ename-input-narrow" name="use_matter_resp">
                        <input type="radio" name="use_matter_resp" id="use_matter_resp" value="1" {{ $enameInfo->use_matter_resp ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="use_matter_resp" id="use_matter_resp" value="0" {{ $enameInfo->use_matter_resp ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td title="{{ $tableComments['country'] }}">Country
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" name="country" data-ac="/country/autocomplete" value="{{ empty($enameInfo->countryInfo) ? '' : $enameInfo->countryInfo->name }}">
                </td><td title="{{ $tableComments['unique'] }}">Is unique
                </td><td><span class="ename-input-narrow" name="unique">
                        <input type="radio" name="unique" id="unique" value="1" {{ $enameInfo->unique ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="unique" id="unique" value="0" {{ $enameInfo->unique ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td title="{{ $tableComments['default_responsible'] }}">Default responsible
                </td><td class="ui-front">
                		<input type="text" class="form-control noformat" data-ac="/user/autocomplete" name="default_responsible" value="{{ empty($enameInfo->default_responsibleInfo) ? "" : $enameInfo->default_responsibleInfo->name }}">
                </td><td title="{{ $tableComments['uqtrigger'] }}">Unique trigger
                </td><td><span class="ename-input-narrow" name="uqtrigger">
                        <input type="radio" name="uqtrigger" id="uqtrigger" value="1" {{ $enameInfo->uqtrigger ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="uqtrigger" id="uqtrigger" value="0" {{ $enameInfo->uqtrigger ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr title="{{ $tableComments['notes'] }}">Notes<br />
                <button type="button" data-field="notes" id="updateNotes" class="area hidden-action btn btn-primary btn-sm">&#9432; Save</button>
                </td><td class="ui-front">
					<textarea data-field="#updateNotes" id="notes" class="form-control editable form-control form-control-sm noformat" name="address_billing">{{ $enameInfo->notes }}</textarea>
				</td></td><td /><td title="{{ $tableComments['killer'] }}">Is killer
                </td><td><span class="ename-input-narrow" name="killer">
                        <input type="radio" name="killer" id="killer" value="1" {{ $enameInfo->killer ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="killer" id="killer" value="0" {{ $enameInfo->killer ? "" : 'checked="checked"'}} />No
                </span>
                </tr>
			</table>
		<button title="Delete event name" id="delete-ename" data-dismiss="modal" data-id="{{ $enameInfo->code }}" style="float: right; margin-top: 10px; margin-right: 16px;">
			&times; Delete
		</button>
	</fieldset>
	
</div>

