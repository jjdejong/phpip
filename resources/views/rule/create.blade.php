<form id="createRuleForm">
	<fieldset>
		<legend>New task rule</legend>
		<table class="table table-sm table-hover">
                <tr><td><label for="task" title="{{ $ruleComments['task'] }}">Task</label> 
                </td><td class="ui-front"><input class="form-control form-control-sm" name="task_new" >
					<input type='hidden' name='task' id='task' >
                </td><td><label for="detail" title="{{ $ruleComments['detail'] }}">Detail</label>
                </td><td><input id="detail" class="form-control form-control-sm" name="detail" >
                </tr><tr><td><label for="for_country_new" title="{{ $ruleComments['for_country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control form-control-sm" name="for_country_new">
                		<input type='hidden' name='for_country' id='for_country' >
                </td><td><label for="is_active" title="{{ $ruleComments['active'] }}">Is active</label>
                </td><td><span class="form-control form-control-sm new" name="active">
                        <input type="radio" name="active" id="is_active" value="1" />YES&nbsp;&nbsp;
                        <input type="radio" name="active" id="is_active" value="0" />NO
                </span>
                </tr><tr><td><label for="for_origin_new" title="{{ $ruleComments['for_origin'] }}">Origin</label>
                </td><td class="ui-front">
                		<input class="form-control form-control-sm" name="for_origin_new">
                		<input type='hidden' name='for_origin' id='for_origin' >
                </td><td><label for="for_category_new" title="{{ $ruleComments['for_category'] }}">Category</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="for_category_new">
						<input type='hidden' name='for_category' id='for_category' >
				</tr><tr><td><label for="notes" title="{{ $ruleComments['notes'] }}">Notes</label>
                </td><td><input id="notes" class="form-control form-control-sm" name="notes">
                </td><td><label for="for_type_new" title="{{ $ruleComments['for_type'] }}">Type</label>
                </td><td class="ui-front">
					<input class="form-control form-control-sm" name="for_type_new" >
					<input type='hidden' name='for_type' id='for_type' >
				</td></tr>
				</table>
        </fieldset>
        <fieldset>
                <legend>Rule details</legend>
                <table class="table table-sm table-hover">
                <tr><td><label for="trigger_event_new" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label>
                </td><td class="ui-front">
					<input class="form-control form-control-sm" name="trigger_event_new">
					<input type='hidden' name='trigger_event' id='trigger_event' >
                </td><td><label for="use_parent" title="{{ $ruleComments['use_parent'] }}">Use parent</label>
                </td><td><span class="form-control form-control-sm" name="use_parent">
                        <input type="radio" name="use_parent" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_parent" value="0"/>NO
                </span>
                </tr><tr><td><label for="use_before" title="{{ $ruleComments['use_before'] }}">Use before</label>
                </td><td><input type='text' class="form-control form-control-sm" size="10" name="use_before" value="...">
                </td><td><label for="clear_task" title="{{ $ruleComments['clear_task'] }}">Clear task</label> 
                </td><td><span class="form-control form-control-sm" name="clear_task">
                        <input type="radio" name="clear_task" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="clear_task" value="0"/>NO
                </span>
                </tr><tr><td><label  for="use_after" title="{{ $ruleComments['use_after'] }}">Use after</label>
                </td><td><input type='text' class="form-control form-control-sm " size="10" name="use_after" value="...">
                </td><td><label for="delete_task" title="{{ $ruleComments['delete_task'] }}">Delete task</label> 
                </td><td><span class="form-control form-control-sm" name="delete_task">
                        <input type="radio" name="delete_task" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="delete_task" value="0"/>NO
                </span>
                </tr><tr><td><label for="condition_event_new" title="{{ $ruleComments['condition_event'] }}">Condition event</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="condition_event_new">
						<input type='hidden' name='condition_event' id='condition_event' >
				</td><td><label for="use_priority" title="{{ $ruleComments['use_priority'] }}">Use priority</label>
                </td><td><span class="form-control form-control-sm" name="use_priority">
                        <input type="radio" name="use_priority" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_priority" value="0"/>NO
                </span>
                </tr><tr><td><label for="abort_on_new" title="{{ $ruleComments['abort_on'] }}">Abort on</label>
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="abort_on_new">
						<input type='hidden' name='abort_on' id='abort_on' >
                </td><td><label for="responsible" title="{{ $ruleComments['responsible'] }}">Responsible</label>
                </td><td class="ui-front" id="responsible" >
						<input type='text' class="form-control form-control-sm" name="responsible_new">
						<input type='hidden' name='responsible' id='responsible' >
				</tr><tr><td><label for="days" title="{{ $ruleComments['days'] }}">Days</label>
                </td><td><input type='number' min="0" class=" form-control form-control-sm" name="days">
                </td><td><label for="cost" title="{{ $ruleComments['cost'] }}">Cost</label> 
                </td><td><input class="form-control form-control-sm" name="cost">
                </tr><tr><td><label for="months" title="{{ $ruleComments['months'] }}">Months</label>
                </td><td><input type='number' min="1" max="12" class=" form-control form-control-sm" name="months">
                </td><td><label for="fee" title="{{ $ruleComments['fee'] }}">Fee</label> 
                </td><td><input type='text' class=" form-control form-control-sm" name="fee">
                </tr><tr><td><label for="years" title="{{ $ruleComments['years'] }}">Years</label>
                </td><td><input class="form-control form-control-sm" type='number' name="years">
                </td><td><label for="currency" title="{{ $ruleComments['currency'] }}">Currency</label>
                </td><td><input type="text" maxlength="3" class="form-control  form-control form-control-sm" name="currency">
                </tr><tr><td><label for="end_of_month" title="{{ $ruleComments['end_of_month'] }}">End of month</label>
                </td><td><span class="form-control form-control-sm" name="end_of_month">
                        <input type="radio" name="end_of_month" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="end_of_month" value="0"/>NO
                </span>
                </td></tr>
        </table>
	</fieldset>
    <div id="error-box">
    </div>
	<button type='submit'>Create rule</button>
</form>

