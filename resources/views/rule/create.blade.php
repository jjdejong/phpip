    
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

.rule-info-set {
	background: #EFEFEF;
	border: 1px inset #888;
}

input {
	border: 0px;
}
</style>

<script type="text/javascript">
$(document).ready(function(){


    $('.close-button').click(function(){
        $('#rule-details-popup').hide();
    });
    
    $( "button, input:button" ).button();

});
</script>
<form id="createRuleForm">
	<fieldset class="rule-info-set">
		<legend>New task rule</legend>
		<table>
                <tr><td><label for="task" title="{{ $ruleComments['task'] }}">Task</label> 
                </td><td class="ui-front"><input class="rule-input-wide" name="task_new" >
					<input type='hidden' name='task' id='task' >
                </td><td><label for="detail" title="{{ $ruleComments['detail'] }}">Detail</label>
                </td><td><input id="detail" class="rule-input-narrow noformat" name="detail" >
                </tr><tr><td><label for="for_country_new" title="{{ $ruleComments['for_country'] }}">Country</label>
                </td><td class="ui-front">
                		<input type="text" class="rule-input-wide" name="for_country_new">
                		<input type='hidden' name='for_country' id='for_country' >
                </td><td><label for="is_active" title="{{ $ruleComments['active'] }}">Is active</label>
                </td><td><span class="rule-input-narrow new" name="active">
                        <input type="radio" name="active" id="is_active" value="1" />YES&nbsp;&nbsp;
                        <input type="radio" name="active" id="is_active" value="0" />NO
                </span>
                </tr><tr><td><label for="for_origin_new" title="{{ $ruleComments['for_origin'] }}">Origin</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="for_origin_new">
                		<input type='hidden' name='for_origin' id='for_origin' >
                </td><td><label for="for_category_new" title="{{ $ruleComments['for_category'] }}">Category</label>
                </td><td class="ui-front">
						<input class="rule-input-narrow" name="for_category_new">
						<input type='hidden' name='for_category' id='for_category' >
				</tr><tr><td><label for="notes" title="{{ $ruleComments['notes'] }}">Notes</label>
                </td><td><input id="notes" class="rule-input-wide noformat" name="notes">
                </td><td><label for="for_type_new" title="{{ $ruleComments['for_type'] }}">Type</label>
                </td><td class="ui-front">
					<input class="rule-input-narrow" name="for_type_new" >
					<input type='hidden' name='for_type' id='for_type' >
				</td></tr>
				</table>
        </fieldset>
        <fieldset class="rule-info-set">
                <legend>Rule details</legend>
                <table>
                <tr><td><label for="trigger_event_new" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label>
                </td><td class="ui-front">
					<input class="rule-input-wide" name="trigger_event_new">
					<input type='hidden' name='trigger_event' id='trigger_event' >
                </td><td><label for="use_parent" title="{{ $ruleComments['use_parent'] }}">Use parent</label>
                </td><td><span class="rule-input-narrow " name="use_parent">
                        <input type="radio" name="use_parent" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_parent" value="0"/>NO
                </span>
                </tr><tr><td><label for="use_before" title="{{ $ruleComments['use_before'] }}">Use before</label>
                </td><td><input type='text' class="rule-input-wide noformat form-control" size="10" name="use_before" value="...">
                </td><td><label for="clear_task" title="{{ $ruleComments['clear_task'] }}">Clear task</label> 
                </td><td><span class="rule-input-narrow " name="clear_task">
                        <input type="radio" name="clear_task" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="clear_task" value="0"/>NO
                </span>
                </tr><tr><td><label  for="use_after" title="{{ $ruleComments['use_after'] }}">Use after</label>
                </td><td><input type='text' class="rule-input-wide noformat form-control" size="10" name="use_after" value="...">
                </td><td><label for="delete_task" title="{{ $ruleComments['delete_task'] }}">Delete task</label> 
                </td><td><span class="rule-input-narrow " name="delete_task">
                        <input type="radio" name="delete_task" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="delete_task" value="0"/>NO
                </span>
                </tr><tr><td><label for="condition_event_new" title="{{ $ruleComments['condition_event'] }}">Condition event</label>
                </td><td class="ui-front">
						<input class="rule-input-wide" name="condition_event_new">
						<input type='hidden' name='condition_event' id='condition_event' >
				</td><td><label for="use_priority" title="{{ $ruleComments['use_priority'] }}">Use priority</label>
                </td><td><span class="rule-input-narrow " name="use_priority">
                        <input type="radio" name="use_priority" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="use_priority" value="0"/>NO
                </span>
                </tr><tr><td><label for="abort_on_new" title="{{ $ruleComments['abort_on'] }}">Abort on</label>
                </td><td class="ui-front">
						<input class="rule-input-wide" name="abort_on_new">
						<input type='hidden' name='abort_on' id='abort_on' >
                </td><td><label for="responsible" title="{{ $ruleComments['responsible'] }}">Responsible</label>
                </td><td class="ui-front" id="responsible" >
						<input type='text' class="rule-input-narrow" name="responsible_new">
						<input type='hidden' name='responsible' id='responsible' >
				</tr><tr><td><label for="days" title="{{ $ruleComments['days'] }}">Days</label>
                </td><td><input type='number' min="0" class="noformat rule-input-wide" name="days">
                </td><td><label for="cost" title="{{ $ruleComments['cost'] }}">Cost</label> 
                </td><td><input class="rule-input-narrow noformat" name="cost">
                </tr><tr><td><label for="months" title="{{ $ruleComments['months'] }}">Months</label>
                </td><td><input type='number' min="1" max="12" class="noformat rule-input-wide" name="months">
                </td><td><label for="fee" title="{{ $ruleComments['fee'] }}">Fee</label> 
                </td><td><input type='text' class="noformat rule-input-narrow" name="fee">
                </tr><tr><td><label for="years" title="{{ $ruleComments['years'] }}">Years</label>
                </td><td><input class="rule-input-wide noformat" type='number' name="years">
                </td><td><label for="currency" title="{{ $ruleComments['currency'] }}">Currency</label>
                </td><td><input type="text" maxlength="3" class="form-control noformat rule-input-narrow" name="currency">
                </tr><tr><td><label for="end_of_month" title="{{ $ruleComments['end_of_month'] }}">End of month</label>
                </td><td><span class="rule-input-narrow" name="end_of_month">
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

