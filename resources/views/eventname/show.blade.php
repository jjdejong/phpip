    
<style>
.actor-input-wide {
	display: inline-block;
	width: 200px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.actor-input-narrow {
	display: inline-block;
	width: 125px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.teditable {
	min-height: 32px;
}

.close-button {
	background: #f00;
	float: right;
	padding: 2px 4px 0px;
	cursor: pointer;
	font-family: arial;
}

.validation-errors {
	color: #F00;
	padding: 5px;
}

#valid-error {
	display: block;
	margin: 0px 0px 5px 10px;
}

.actor-info-set {
	background: #EFEFEF;
	border: 1px inset #888;
}

input {
	border: 0px;
}
</style>

<div id="edit-ename-content">
	<fieldset class="ename-info-set">
		<legend>Event name details - ID: {{ $enameInfo->code }}</legend>
		<table data-id="{{ $enameInfo->code }}" data-source="/eventname/">
                <tr><td><label for="code" class="required-field" title="{{ $tableComments['code'] }}">Code</label> 
                </td><td>{{ $enameInfo->code }}
                </td><td><label for="is_task" title="{{ $tableComments['is_task'] }}">Is task</label>
                </td><td><span class="ename-input-narrow" name="is_task">
                        <input type="radio" name="is_task" id="is_task" value="1" {{ $enameInfo->is_task ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="is_task" id="is_task" value="0" {{ $enameInfo->is_task ? "" : 'checked="checked"'}} />No
                </span>
                </td></tr><tr><td><label for="name" title="{{ $tableComments['name'] }}">Name</label>
                </td><td><input id="name" class="ename-input-wide noformat" name="name" value="{{ $enameInfo->name }}">
                </td><td><label for="status_event" title="{{ $tableComments['status_event'] }}">Is status event</label>
                </td><td><span class="ename-input-narrow" name="status_event">
                        <input type="radio" name="status_event" id="status_event" value="1" {{ $enameInfo->status_event ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="status_event" id="status_event" value="0" {{ $enameInfo->status_event ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="category" title="{{ $tableComments['category'] }}">Category</label>
                </td><td class="ui-front">
                		<input type="text" class="ename-input-wide" name="category" value="{{ empty($enameInfo->categoryInfo) ? '' : $enameInfo->categoryInfo->category }}">
                </td><td><label for="use_matter_resp" title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible</label>
                </td><td><span class="ename-input-narrow" name="use_matter_resp">
                        <input type="radio" name="use_matter_resp" id="use_matter_resp" value="1" {{ $enameInfo->use_matter_resp ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="use_matter_resp" id="use_matter_resp" value="0" {{ $enameInfo->use_matter_resp ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="country" title="{{ $tableComments['country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="ename-input-wide" name="country" value="{{ empty($enameInfo->countryInfo) ? "" : $enameInfo->countryInfo->name }}">
                </td><td><label for="unique" title="{{ $tableComments['unique'] }}">Is unique</label>
                </td><td><span class="ename-input-narrow" name="unique">
                        <input type="radio" name="unique" id="unique" value="1" {{ $enameInfo->unique ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="unique" id="unique" value="0" {{ $enameInfo->unique ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="default_responsible" title="{{ $tableComments['default_responsible'] }}">Default responsible</label>
                </td><td class="ui-front">
                		<input type="text" class="ename-input-wide noformat" name="default_responsible" value="{{ empty($enameInfo->default_responsibleInfo) ? "" : $enameInfo->default_responsibleInfo->name }}">
                </td><td><label for="uqtrigger" title="{{ $tableComments['uqtrigger'] }}">Unique trigger</label>
                </td><td><span class="ename-input-narrow" name="uqtrigger">
                        <input type="radio" name="uqtrigger" id="uqtrigger" value="1" {{ $enameInfo->uqtrigger ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="uqtrigger" id="uqtrigger" value="0" {{ $enameInfo->uqtrigger ? "" : 'checked="checked"'}} />No
                </span>
                </tr><tr><td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label><br />
                <button type="button" data-field="notes" id="updateNotes" class="area hidden-action btn btn-primary btn-sm">&#9432; Save</button>
                </td><td class="ui-front">
					<textarea data-field="#updateNotes" id="notes" class="ename-input-wide editable form-control form-control-sm" name="address_billing">{{ $enameInfo->notes }}</textarea>
				</td></td><td><label for="killer" title="{{ $tableComments['killer'] }}">Is killer</label>
                </td><td><span class="ename-input-narrow" name="killer">
                        <input type="radio" name="killer" id="killer" value="1" {{ $enameInfo->killer ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="killer" id="killer" value="0" {{ $enameInfo->killer ? "" : 'checked="checked"'}} />No
                </span>
                </tr>
			</table>
		<button title="Delete event name" id="delete-ename" data-dismiss="modal" data-id="{{ $enameInfo->code }}" style="float: right; margin-top: 10px; margin-right: 16px;">
			<span class="ui-icon ui-icon-trash" style="float: left;"></span>
			Delete
		</button>
	</fieldset>
	
</div>

