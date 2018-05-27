    
<style>
.rule-input-wide {
	display: inline-block;
	width: 200px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.rule-input-narrow {
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

.rule-info-set {
	background: #EFEFEF;
	border: 1px inset #888;
}

input {
	border: 0px;
}
</style>

<div id="edit-rule-content">
	<input type="hidden" name="rule_id" id="rule-id"
		value="{{ $ruleInfo->id }}" />   
	<fieldset class="rule-info-set">
		<legend>Task rule details - ID: {{ $ruleInfo->id }}</legend>
		<table data-id="{{ $ruleInfo->id }}">
                <tr><td><label for="task" class="required-field" title="{{ $ruleComments['task'] }}">Task</label> 
                </td><td class="ui-front"><input class="rule-input-wide" name="task" value="{{ empty($ruleInfo->taskInfo) ? '' : $ruleInfo->taskInfo->name }}">
                </td><td><label for="detail" title="{{ $ruleComments['detail'] }}">Detail</label>
                </td><td><input id="detail" class="rule-input-narrow noformat" name="detail" value="{{ $ruleInfo->detail }}">
                </tr><tr><td><label for="for_country" title="{{ $ruleComments['for_country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="rule-input-wide" name="for_country" value="{{ empty($ruleInfo->country) ? '' : $ruleInfo->country->name }}">
                </td><td><label for="is_active" title="{{ $ruleComments['active'] }}">Is active</label>
                </td><td><span class="rule-input-narrow" name="active">
                        <input type="radio" name="active" id="is_active" value="1" {{ $ruleInfo->active ? 'checked="checked"' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="active" id="is_active" value="0" {{ $ruleInfo->active ? "" : 'checked="checked"'}} />NO
                </span>
                </tr><tr><td><label for="for_origin" title="{{ $ruleComments['for_origin'] }}">Origin</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="for_origin" value="{{ empty($ruleInfo->origin) ? '' : $ruleInfo->origin->name }}">
                </td><td><label for="for_category" title="{{ $ruleComments['for_category'] }}">Category</label>
                </td><td class="ui-front">
						<input class="rule-input-narrow" name="for_category" value="{{ empty($ruleInfo->category) ? '' : $ruleInfo->category->category }}">
				</tr><tr><td><label for="notes" title="{{ $ruleComments['notes'] }}">Notes</label>
                </td><td><input id="notes" class="rule-input-wide noformat" name="notes" value="{{ $ruleInfo->notes }}">
                </td><td><label for="for_type" title="{{ $ruleComments['for_type'] }}">Type</label>
                </td><td class="ui-front">
					<input class="rule-input-narrow" name="for_type" value="{{ empty($ruleInfo->type) ? '' : $ruleInfo->type->type }}">
				</td></tr>
				</table>
        </fieldset>
        <fieldset class="rule-info-set">
                <legend>Rule details</legend>
                <table data-id="{{ $ruleInfo->id }}">
                <tr><td><label for="trigger_event" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label>
                </td><td class="ui-front">
					<input class="rule-input-wide" name="trigger_event" value="{{ empty($ruleInfo->trigger) ? '' : $ruleInfo->trigger->name }}">
                </td><td><label for="use_parent" title="{{ $ruleComments['use_parent'] }}">Use parent</label>
                </td><td><span class="rule-input-narrow " name="use_parent">
                        <input type="radio" name="use_parent" value="1" {{ $ruleInfo->use_parent ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_parent" value="0" {{ $ruleInfo->use_parent ? '' :'checked=checked' }} />NO
                </span>
                </tr><tr><td><label for="use_before" title="{{ $ruleComments['use_before'] }}">Use before</label>
                </td><td><input type='text' class="rule-input-wide noformat form-control" size="10" name="use_before" value="{{ $ruleInfo->use_before != '' ?  $ruleInfo->use_before: '...' }}">
                </td><td><label for="clear_task" title="{{ $ruleComments['clear_task'] }}">Clear task</label> 
                </td><td><span class="rule-input-narrow " name="clear_task">
                        <input type="radio" name="clear_task" value="1" {{ $ruleInfo->clear_task ? 'checked=checked' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="clear_task" value="0" {{ $ruleInfo->clear_task ? "" : 'checked=checked' }} />NO
                </span>
                </tr><tr><td><label  for="use_after" title="{{ $ruleComments['use_after'] }}">Use after</label>
                </td><td><input type='text' class="rule-input-wide noformat form-control" size="10" name="use_after" value="{{ $ruleInfo->use_after != "" ?  $ruleInfo->use_after: "..." }}">
                </td><td><label for="delete_task" title="{{ $ruleComments['delete_task'] }}">Delete task</label> 
                </td><td><span class="rule-input-narrow " name="delete_task">
                        <input type="radio" name="delete_task" value="1" {{ $ruleInfo->delete_task ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="delete_task" value="0" {{ $ruleInfo->delete_task ? "" : 'checked=checked' }}/>NO
                </span>
                </tr><tr><td><label for="condition_event" title="{{ $ruleComments['condition_event'] }}">Condition event</label>
                </td><td class="ui-front">
						<input class="rule-input-wide" name="condition_event" value="{{ empty($ruleInfo->condition_eventInfo) ? '' : $ruleInfo->condition_eventInfo->name }}">
				</td><td><label for="use_priority" title="{{ $ruleComments['use_priority'] }}">Use priority</label>
                </td><td><span class="rule-input-narrow " name="use_priority">
                        <input type="radio" name="use_priority" value="1" {{ $ruleInfo->use_priority ? 'checked=checked' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="use_priority" value="0" {{ $ruleInfo->use_priority ? "" : 'checked=checked' }}/>NO
                </span>
                </tr><tr><td><label for="abort_on" title="{{ $ruleComments['abort_on'] }}">Abort on</label>
                </td><td class="ui-front">
						<input class="rule-input-wide" name="abort_on" value="{{ empty($ruleInfo->abort_onInfo) ? '' : $ruleInfo->abort_onInfo->name }}">
                </td><td><label type='text' for="responsible" title="{{ $ruleComments['responsible'] }}">Responsible</label>
                </td><td class="ui-front">
						<input class="rule-input-narrow noformat" name="responsible" value="{{ empty($ruleInfo->responsibleInfo) ? '' : $ruleInfo->responsibleInfo->name }}">
				</tr><tr><td><label for="days" title="{{ $ruleComments['days'] }}">Days</label>
                </td><td><input type='number' min="0" class="noformat rule-input-wide" name="days" value="{{ $ruleInfo->days }}">
                </td><td><label for="cost" title="{{ $ruleComments['cost'] }}">Cost</label> 
                </td><td><input class="rule-input-narrow noformat" name="cost" value="{{ $ruleInfo->cost }}">
                </tr><tr><td><label for="months" title="{{ $ruleComments['months'] }}">Months</label>
                </td><td><input type='number' min="1" max="12" class="noformat rule-input-wide" name="months" value="{{ $ruleInfo->months }}">
                </td><td><label for="fee" title="{{ $ruleComments['fee'] }}">Fee</label> 
                </td><td><input type='text' class="noformat rule-input-narrow" name="fee" value="{{ $ruleInfo->fee }}">
                </tr><tr><td><label for="years" title="{{ $ruleComments['years'] }}">Years</label>
                </td><td><input class="rule-input-wide noformat" type='number' name="years" value="{{ $ruleInfo->years }}">
                </td><td><label for="currency" title="{{ $ruleComments['currency'] }}">Currency</label>
                </td><td><input type="text" maxlength="3" class="form-control noformat rule-input-narrow" name="currency" value="{{ $ruleInfo->currency }}">
                </tr><tr><td><label for="end_of_month" title="{{ $ruleComments['end_of_month'] }}">End of month</label>
                </td><td><span class="rule-input-narrow" name="end_of_month">
                        <input type="radio" name="end_of_month" value="1" {{ $ruleInfo->end_of_month ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="end_of_month" value="0" {{ $ruleInfo->end_of_month ? "" : 'checked=checked' }}/>NO
                </span>
                </td></tr>
        </table>
		<button title="Delete rule" id="delete-rule" data-dismiss="modal" data-id="{{ $ruleInfo->id }}" style="float: right; margin-top: 10px; margin-right: 16px;">
			<span class="ui-icon ui-icon-trash" style="float: left;"></span>
			Delete
		</button>
	</fieldset>
	
</div>

