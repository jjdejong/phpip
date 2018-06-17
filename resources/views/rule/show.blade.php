<div id="edit-rule-content">
	<input type="hidden" name="rule_id" id="rule-id"
		value="{{ $ruleInfo->id }}" />   
	<fieldset>
		<legend>Task rule details - ID: {{ $ruleInfo->id }}</legend>
		<table class="table table-sm table-hover" data-id="{{ $ruleInfo->id }}">
                <tr><td><label for="task" class="required-field" title="{{ $ruleComments['task'] }}">Task</label> 
                </td><td class="ui-front"><input class="form-control form-control-sm" name="task" value="{{ empty($ruleInfo->taskInfo) ? '' : $ruleInfo->taskInfo->name }}">
                </td><td><label for="detail" title="{{ $ruleComments['detail'] }}">Detail</label>
                </td><td><input id="detail" class="form-control form-control-sm editable" name="detail" value="{{ $ruleInfo->detail }}">
                </tr><tr><td><label for="for_country" title="{{ $ruleComments['for_country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control form-control-sm" name="for_country" value="{{ empty($ruleInfo->country) ? '' : $ruleInfo->country->name }}">
                </td><td><label for="is_active" title="{{ $ruleComments['active'] }}">Is active</label>
                </td><td><span class="form-control form-control-sm" name="active">
                        <input type="radio" name="active" id="is_active" value="1" {{ $ruleInfo->active ? 'checked="checked"' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="active" id="is_active" value="0" {{ $ruleInfo->active ? "" : 'checked="checked"'}} />NO
                </span>
                </tr><tr><td><label for="for_origin" title="{{ $ruleComments['for_origin'] }}">Origin</label>
                </td><td class="ui-front">
                		<input class="form-control form-control-sm" name="for_origin" value="{{ empty($ruleInfo->origin) ? '' : $ruleInfo->origin->name }}">
                </td><td><label for="for_category" title="{{ $ruleComments['for_category'] }}">Category</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="for_category" value="{{ empty($ruleInfo->category) ? '' : $ruleInfo->category->category }}">
				</tr><tr><td><label for="notes" title="{{ $ruleComments['notes'] }}">Notes</label>
                </td><td><input id="notes" class="form-control form-control-sm editable" name="notes" value="{{ $ruleInfo->notes }}">
                </td><td><label for="for_type" title="{{ $ruleComments['for_type'] }}">Type</label>
                </td><td class="ui-front">
					<input class="form-control form-control-sm" name="for_type" value="{{ empty($ruleInfo->type) ? '' : $ruleInfo->type->type }}">
				</td></tr>
				</table>
        </fieldset>
        <fieldset>
                <legend>Rule details</legend>
                <table class="table table-sm table-hover" data-id="{{ $ruleInfo->id }}">
                <tr><td><label for="trigger_event" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label>
                </td><td class="ui-front">
					<input class="form-control form-control-sm" name="trigger_event" value="{{ empty($ruleInfo->trigger) ? '' : $ruleInfo->trigger->name }}">
                </td><td><label for="use_parent" title="{{ $ruleComments['use_parent'] }}">Use parent</label>
                </td><td><span class="form-control form-control-sm" name="use_parent">
                        <input type="radio" name="use_parent" value="1" {{ $ruleInfo->use_parent ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_parent" value="0" {{ $ruleInfo->use_parent ? '' :'checked=checked' }} />NO
                </span>
                </tr><tr><td><label for="use_before" title="{{ $ruleComments['use_before'] }}">Use before</label>
                </td><td><input type='text' class="form-control form-control-sm form-control" size="10" name="use_before" value="{{ $ruleInfo->use_before != '' ?  $ruleInfo->use_before: '...' }}">
                </td><td><label for="clear_task" title="{{ $ruleComments['clear_task'] }}">Clear task</label> 
                </td><td><span class="form-control form-control-sm" name="clear_task">
                        <input type="radio" name="clear_task" value="1" {{ $ruleInfo->clear_task ? 'checked=checked' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="clear_task" value="0" {{ $ruleInfo->clear_task ? "" : 'checked=checked' }} />NO
                </span>
                </tr><tr><td><label  for="use_after" title="{{ $ruleComments['use_after'] }}">Use after</label>
                </td><td><input type='text' class="form-control form-control-sm form-control" size="10" name="use_after" value="{{ $ruleInfo->use_after != "" ?  $ruleInfo->use_after: "..." }}">
                </td><td><label for="delete_task" title="{{ $ruleComments['delete_task'] }}">Delete task</label> 
                </td><td><span class="form-control form-control-sm" name="delete_task">
                        <input type="radio" name="delete_task" value="1" {{ $ruleInfo->delete_task ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="delete_task" value="0" {{ $ruleInfo->delete_task ? "" : 'checked=checked' }}/>NO
                </span>
                </tr><tr><td><label for="condition_event" title="{{ $ruleComments['condition_event'] }}">Condition event</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="condition_event" value="{{ empty($ruleInfo->condition_eventInfo) ? '' : $ruleInfo->condition_eventInfo->name }}">
				</td><td><label for="use_priority" title="{{ $ruleComments['use_priority'] }}">Use priority</label>
                </td><td><span class="form-control form-control-sm" name="use_priority">
                        <input type="radio" name="use_priority" value="1" {{ $ruleInfo->use_priority ? 'checked=checked' : "" }} />YES&nbsp;&nbsp;
                        <input type="radio" name="use_priority" value="0" {{ $ruleInfo->use_priority ? "" : 'checked=checked' }}/>NO
                </span>
                </tr><tr><td><label for="abort_on" title="{{ $ruleComments['abort_on'] }}">Abort on</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="abort_on" value="{{ empty($ruleInfo->abort_onInfo) ? '' : $ruleInfo->abort_onInfo->name }}">
                </td><td><label type='text' for="responsible" title="{{ $ruleComments['responsible'] }}">Responsible</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="responsible" value="{{ empty($ruleInfo->responsibleInfo) ? '' : $ruleInfo->responsibleInfo->name }}">
				</tr><tr><td><label for="days" title="{{ $ruleComments['days'] }}">Days</label>
                </td><td><input type='number' min="0" class="editable form-control form-control-sm" name="days" value="{{ $ruleInfo->days }}">
                </td><td><label for="cost" title="{{ $ruleComments['cost'] }}">Cost</label> 
                </td><td><input class="form-control form-control-sm editable" name="cost" value="{{ $ruleInfo->cost }}">
                </tr><tr><td><label for="months" title="{{ $ruleComments['months'] }}">Months</label>
                </td><td><input type='number' min="1" max="12" class="editable form-control form-control-sm" name="months" value="{{ $ruleInfo->months }}">
                </td><td><label for="fee" title="{{ $ruleComments['fee'] }}">Fee</label> 
                </td><td><input type='text' class="editable form-control form-control-sm" name="fee" value="{{ $ruleInfo->fee }}">
                </tr><tr><td><label for="years" title="{{ $ruleComments['years'] }}">Years</label>
                </td><td><input class="form-control form-control-sm editable" type='number' name="years" value="{{ $ruleInfo->years }}">
                </td><td><label for="currency" title="{{ $ruleComments['currency'] }}">Currency</label>
                </td><td><input type="text" maxlength="3" class="form-control editable form-control form-control-sm" name="currency" value="{{ $ruleInfo->currency }}">
                </tr><tr><td><label for="end_of_month" title="{{ $ruleComments['end_of_month'] }}">End of month</label>
                </td><td><span class="form-control form-control-sm" name="end_of_month">
                        <input type="radio" name="end_of_month" value="1" {{ $ruleInfo->end_of_month ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="end_of_month" value="0" {{ $ruleInfo->end_of_month ? "" : 'checked=checked' }}/>NO
                </span>
                </td></tr>
        </table>
		<button title="Delete rule" id="delete-rule" data-dismiss="modal" data-id="{{ $ruleInfo->id }}">
			<span class="ui-icon ui-icon-trash" style="float: left;"></span>
			Delete
		</button>
	</fieldset>
	
</div>

