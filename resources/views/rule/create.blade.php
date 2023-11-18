<form id="createRuleForm">
  <fieldset>
    <legend>{{ _i('New task rule') }}</legend>
    <table class="table table-sm">
      <tr>
        <td><label for="task" title="{{ $ruleComments['task'] }} " class="required-field"><b>{{ _i('Task') }}</b></label></td>
        <td >
          <input type='hidden' name='task'>
          <input type="text" class="form-control form-control-sm" data-ac="/event-name/autocomplete/1" data-actarget="task" autocomplete="off">
        </td>
        <td><label for="detail" title="{{ $ruleComments['detail'] }}">{{ _i('Detail') }}</label></td>
        <td><input id="detail" class="form-control form-control-sm" name="detail"></td>
      </tr>
      <tr>
        <td><label for="for_country_new" title="{{ $ruleComments['for_country'] }}">Country</label></td>
        <td>
          <input type='hidden' name='for_country'>
          <input type="text" class="form-control form-control-sm" data-ac="/country/autocomplete" data-actarget="for_country" autocomplete="off">
        </td>
        <td><label title="{{ $ruleComments['active'] }}">{{ _i('Is active') }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="active" value="1" checked>
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="active" value="0">
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label title="{{ $ruleComments['for_origin'] }}" class="required-field">{{ _i('Origin') }}</label></td>
        <td>
          <input type='hidden' name='for_origin'>
          <input type="text" class="form-control form-control-sm" data-ac="/country/autocomplete" data-actarget="for_origin" autocomplete="off">
        </td>
        <td><label for="for_category_new" title="{{ $ruleComments['for_category'] }}"><b>{{ _i('Category') }}</b></label></td>
        <td>
          <input type='hidden' name='for_category'>
          <input type="text" class="form-control form-control-sm" data-ac="/category/autocomplete" data-actarget="for_category" autocomplete="off">
        </td>
      </tr>
      <tr>
        <td><label for="notes" title="{{ $ruleComments['notes'] }}">{{ _i('Notes') }}</label></td>
        <td><textarea class="form-control form-control-sm" name="notes"></textarea></td>
        <td><label title="{{ $ruleComments['for_type'] }}">{{ _i('Type') }}</label></td>
        <td>
          <input type='hidden' name='for_type'>
          <input type="text" class="form-control form-control-sm" data-ac="/type/autocomplete" data-actarget="for_type" autocomplete="off">
        </td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{{ _i('Rule details') }}</legend>
    <table class="table table-sm">
      <tr>
        <td><label for="trigger_event_new" title="{{ $ruleComments['trigger_event'] }}" class="required-field"><b>{{ _i('Trigger event') }}</b></label></td>
        <td>
          <input type='hidden' name='trigger_event'>
          <input type="text" class="form-control form-control-sm" data-ac="/event-name/autocomplete/0" data-actarget="trigger_event" autocomplete="off">
        </td>
        <td><label title="{{ $ruleComments['end_of_month'] }}">{{ _i('End of month') }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="end_of_month" value="1">
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="end_of_month" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="use_before" title="{{ $ruleComments['use_before'] }}">{{ _i('Use before') }}</label></td>
        <td><input type="date" class="form-control form-control-sm" name="use_before"></td>
        <td><label title="{{ $ruleComments['clear_task'] }}">{{ _i('Clear task') }}</label> </td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="clear_task" value="1">
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="clear_task" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="use_after" title="{{ $ruleComments['use_after'] }}">{{ _i('Use after') }}</label></td>
        <td><input type="date" class="form-control form-control-sm " name="use_after"></td>
        <td><label title="{{ $ruleComments['delete_task'] }}">{{ _i('Delete task') }}</label> </td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="delete_task" value="1">
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="delete_task" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="condition_event_new" title="{{ $ruleComments['condition_event'] }}">{{ _i('Condition event') }}</label></td>
        <td>
          <input type='hidden' name='condition_event'>
          <input type="text" class="form-control form-control-sm" data-ac="/event-name/autocomplete/0" data-actarget="condition_event" autocomplete="off">
        </td>
        <td><label title="{{ $ruleComments['use_priority'] }}">{{ _i('Use priority') }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_priority" value="1">
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_priority" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="abort_on_new" title="{{ $ruleComments['abort_on'] }}">{{ _i('Abort on') }}</label></td>
        <td>
          <input type='hidden' name='abort_on'>
          <input class="form-control form-control-sm" data-ac="/event-name/autocomplete/0" data-actarget="abort_on" autocomplete="off">
        </td>
        <td><label for="responsible" title="{{ $ruleComments['responsible'] }}">{{ _i('Responsible') }}</label></td>
        <td>
          <input type='hidden' name='responsible'>
          <input type='text' class="form-control form-control-sm" data-ac="/user/autocomplete" data-actarget="responsible" autocomplete="off">
        </td>
      </tr>
      <tr>
        <td><label for="days" title="{{ $ruleComments['days'] }}">{{ _i('Days') }}</label></td>
        <td><input type="number" class=" form-control form-control-sm" name="days" value="0"></td>
        <td><label for="cost" title="{{ $ruleComments['cost'] }}">{{ _i('Cost') }}</label> </td>
        <td><input class="form-control form-control-sm" name="cost"></td>
      </tr>
      <tr>
        <td><label for="months" title="{{ $ruleComments['months'] }}">{{ _i('Months') }}</label></td>
        <td><input type="number" class=" form-control form-control-sm" name="months" value="0"></td>
        <td><label for="fee" title="{{ $ruleComments['fee'] }}">{{ _i('Fee') }}</label> </td>
        <td><input type='text' class=" form-control form-control-sm" name="fee"></td>
      </tr>
      <tr>
        <td><label for="years" title="{{ $ruleComments['years'] }}">{{ _i('Years') }}</label></td>
        <td><input class="form-control form-control-sm" type='number' name="years" value="0"></td>
        <td><label for="currency" title="{{ $ruleComments['currency'] }}">{{ _i('Currency') }}</label></td>
        <td><input type="text" maxlength="3" class="form-control form-control-sm" name="currency"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" class="btn btn-primary" id="createRuleSubmit" data-redirect="/rule">Create rule</button><br>
</form>
