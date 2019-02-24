    
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

<form id="createEventForm">
	<fieldset class="rule-info-set">
		<legend>New event name</legend>
		<table>
                <tr><td><label for="code" title="{{ $tableComments['code'] }}"><b>Code</b></label>
                </td><td><input id="code" class="rule-input-wide noformat" name="code" >
                </td><td><label for="is_task" title="{{ $tableComments['is_task'] }}">Is task</label>
                </td><td><span class="rule-input-narrow " name="is_task">
                        <input type="radio" name="is_task" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="is_task" value="0"/> No
                     </span>
				</td></tr>
                <tr><td><label for="name" title="{{ $tableComments['name'] }}"><b>Name</b></label> 
                </td><td><input id="name" class="rule-input-wide noformat" name="name" >
                </td><td><label for="status_event" title="{{ $tableComments['status_event'] }}">Is status event</label>
                </td><td><span class="rule-input-narrow " name="status_event">
                        <input type="radio" name="status_event" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="status_event" value="0"/> No
                     </span>
                </td></tr><tr><td><label for="responsible_new" title="{{ $tableComments['default_responsible'] }}">Default responsible</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="responsible_new">
                		<input type='hidden' name='default_responsible' id='default_responsible' >
                </td><td><label for="use_matter_resp" title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible</label>
                </td><td><span class="rule-input-narrow " name="use_matter_resp">
                        <input type="radio" name="use_matter_resp" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="use_matter_resp" value="0"/> No
                     </span>
                </td></tr><tr><td><label for="country_new" title="{{ $tableComments['country'] }}">Country</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="country_new">
                		<input type='hidden' name='country' id='country' >
                </td><td><label for="unique" title="{{ $tableComments['unique'] }}">Is unique</label>
                </td><td><span class="rule-input-narrow " name="unique">
                        <input type="radio" name="unique" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="unique" value="0"/> No
                     </span>
                <tr><td><label for="category_new" title="{{ $tableComments['category'] }}">Category</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="category_new">
                		<input type='hidden' name='category' id='category' >
                </td><td><label for="uqtrigger" title="{{ $tableComments['uqtrigger'] }}">Unique trigger</label>
                </td><td><span class="rule-input-narrow " name="uqtrigger">
                        <input type="radio" name="uqtrigger" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="uqtrigger" value="0"/> No
                     </span>
				</td></tr><tr><td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label>
                </td><td><textarea id="notes" class="rule-input-wide noformat" name="notes"></textarea>
				</td><td><label for="killer" title="{{ $tableComments['killer'] }}">Is killer</label>
                </td><td><span class="rule-input-narrow " name="killer">
                        <input type="radio" name="killer" value="1"/> Yes&nbsp;&nbsp;
                        <input type="radio" name="killer" value="0"/> No
                     </span>
                 </td></tr>
			</table>
    <div id="error-box">
    </div>
	<button type='submit'>Create event name</button>
</form>

