<div class="card" style="height: 480px;">
  <div class="nav nav-pills" role="tablist">
    <a class="nav-item nav-link active" data-toggle="tab" href="#ruleMain" role="tab">Main</a>
    <a class="nav-item nav-link" data-toggle="tab" href="#ruleDetail" role="tab">Details</a>
    <button id="deleteRule" title="Delete rule" class="nav-item nav-link btn btn-outline-danger" data-dismiss="modal" data-id="{{ $ruleInfo->id }}">
      Delete
    </button>
  </div>
  <div class="tab-content" data-resource="/rule/{{ $ruleInfo->id }}">
    <fieldset class="tab-pane fade show active" id="ruleMain">
      <table class="table table-sm table-hover" data-id="{{ $ruleInfo->id }}" data-source="/rule/">
        <tr>
          <td><label for="trigger_event" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label></td>
          <td><input type="text" class="form-control noformat" name="trigger_event" list="ajaxDatalist" data-ac="/event-name/autocomplete/0" placeholder="{{ $ruleInfo->trigger->name }}"></td>
        </tr>
        <tr>
          <td><label for="task" class="required-field" title="{{ $ruleComments['task'] }}">Task</label></td>
          <td><input type="text" class="form-control noformat" name="task" list="ajaxDatalist" data-ac="/event-name/autocomplete/1" placeholder="{{ $ruleInfo->taskInfo->name }}"></td>
        </tr>
        <tr>
          <td><label for="detail" title="{{ $ruleComments['detail'] }}">Detail</label></td>
          <td><input class="form-control noformat" name="detail" value="{{ $ruleInfo->detail }}"></td>
        </tr>
        <tr>
          <td><label for="for_category" title="{{ $ruleComments['for_category'] }}">Category</label></td>
          <td><input type="text" class="form-control noformat" name="for_category" list="ajaxDatalist" data-ac="/category/autocomplete" value="{{ empty($ruleInfo->category) ? '' : $ruleInfo->category->category }}"></td>
        </tr>
        <tr>
          <td><label for="for_country" title="{{ $ruleComments['for_country'] }}">Country</label></td>
          <td><input type="text" class="form-control noformat" name="for_country" list="ajaxDatalist" data-ac="/country/autocomplete" value="{{ empty($ruleInfo->country) ? '' : $ruleInfo->country->name }}"></td>
        </tr>
        <tr>
          <td><label for="for_origin" title="{{ $ruleComments['for_origin'] }}">Origin</label></td>
          <td><input type="text" class="form-control noformat" name="for_origin" list="ajaxDatalist" data-ac="/country/autocomplete" value="{{ empty($ruleInfo->origin) ? '' : $ruleInfo->origin->name }}"></td>
        </tr>
        <tr>
          <td><label for="for_type" title="{{ $ruleComments['for_type'] }}">Type</label></td>
          <td><input type="text" class="form-control noformat" name="for_type" list="ajaxDatalist" data-ac="/type/autocomplete" value="{{ empty($ruleInfo->type) ? '' : $ruleInfo->type->type }}"></td>
        </tr>
        <tr>
          <td><label for="is_active" title="{{ $ruleComments['active'] }}">Is active</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="active" value="1" {{ $ruleInfo->active ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="active" value="0" {{ $ruleInfo->active ? '' : 'checked' }}>
              <label class="form-check-label" for="is_active">No</label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label for="notes" title="{{ $ruleComments['notes'] }}">Notes</label><br /></td>
          <td><textarea class="form-control noformat" name="notes" rows="4">{{ $ruleInfo->notes }}</textarea></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="tab-pane fade" id="ruleDetail">
      <table class="table table-sm table-hover" data-id="{{ $ruleInfo->id }}">
        <tr>
          <td><label for="trigger_event" title="{{ $ruleComments['trigger_event'] }}">Trigger event</label></td>
          <td><input type="text" class="form-control noformat" name="trigger_event" list="ajaxDatalist" data-ac="/event-name/autocomplete/0" placeholder="{{ $ruleInfo->trigger->name }}"></td>
          <td><label for="use_parent" title="{{ $ruleComments['use_parent'] }}">Use parent</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="use_parent" value="1" {{ $ruleInfo->use_parent ? 'checked' : '' }}>
              <label class="form-check-label" for="use_parent">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="use_parent" value="0" {{ $ruleInfo->use_parent ? '' : 'checked' }}>
              <label class="form-check-label" for="use_parent">No</label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label for="use_before" title="{{ $ruleComments['use_before'] }}">Use before</label></td>
          <td><input type="date" class="form-control noformat" name="use_before" value="{{ $ruleInfo->use_before != '' ?  $ruleInfo->use_before: '...' }}"></td>
          <td><label for="clear_task" title="{{ $ruleComments['clear_task'] }}">Clear task</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="clear_task" value="1" {{ $ruleInfo->clear_task ? 'checked' : '' }}>
              <label class="form-check-label" for="clear_task">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="clear_task" value="0" {{ $ruleInfo->clear_task ? '' : 'checked' }}>
              <label class="form-check-label" for="clear_task">No</label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label for="use_after" title="{{ $ruleComments['use_after'] }}">Use after</label></td>
          <td><input type="date" class="form-control noformat" name="use_after" value="{{ $ruleInfo->use_after != "" ?  $ruleInfo->use_after: "..." }}"></td>
          <td><label for="delete_task" title="{{ $ruleComments['delete_task'] }}">Delete task</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="delete_task" value="1" {{ $ruleInfo->delete_task ? 'checked' : '' }}>
              <label class="form-check-label" for="delete_task">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="delete_task" value="0" {{ $ruleInfo->delete_task ? '' : 'checked' }}>
              <label class="form-check-label" for="delete_task">No</label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label for="condition_event" title="{{ $ruleComments['condition_event'] }}">Condition event</label></td>
          <td><input type="text" class="form-control noformat" name="condition_event" list="ajaxDatalist" data-ac="/event-name/autocomplete/0" value="{{ empty($ruleInfo->condition_eventInfo) ? '' : $ruleInfo->condition_eventInfo->name }}"></td>
          <td><label for="use_priority" title="{{ $ruleComments['use_priority'] }}">Use priority</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="use_priority" value="1" {{ $ruleInfo->use_priority ? 'checked' : '' }}>
              <label class="form-check-label" for="use_priority">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="use_priority" value="0" {{ $ruleInfo->use_priority ? '' : 'checked' }}>
              <label class="form-check-label" for="use_priority">No</label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label for="abort_on" title="{{ $ruleComments['abort_on'] }}">Abort on</label></td>
          <td><input type="text" class="form-control noformat" name="abort_on" list="ajaxDatalist" data-ac="/event-name/autocomplete/0" value="{{ empty($ruleInfo->abort_onInfo) ? '' : $ruleInfo->abort_onInfo->name }}"></td>
          <td><label for="responsible" title="{{ $ruleComments['responsible'] }}">Responsible</label></td>
          <td><input type="text" class="form-control noformat" name="responsible" list="ajaxDatalist" data-ac="/user/autocomplete" value="{{ empty($ruleInfo->responsibleInfo) ? '' : $ruleInfo->responsibleInfo->name }}"></td>
        </tr>
        <tr>
          <td><label for="days" title="{{ $ruleComments['days'] }}">Days</label></td>
          <td><input class="form-control noformat" name="days" value="{{ $ruleInfo->days }}"></td>
          <td><label for="cost" title="{{ $ruleComments['cost'] }}">Cost</label></td>
          <td><input class="form-control noformat" name="cost" value="{{ $ruleInfo->cost }}"></td>
        </tr>
        <tr>
          <td><label for="months" title="{{ $ruleComments['months'] }}">Months</label></td>
          <td><input class="form-control noformat" name="months" value="{{ $ruleInfo->months }}"></td>
          <td><label for="fee" title="{{ $ruleComments['fee'] }}">Fee</label></td>
          <td><input class="form-control noformat" name="fee" value="{{ $ruleInfo->fee }}"></td>
        </tr>
        <tr>
          <td><label for="years" title="{{ $ruleComments['years'] }}">Years</label></td>
          <td><input class="form-control noformat" name="years" value="{{ $ruleInfo->years }}"></td>
          <td><label for="currency" title="{{ $ruleComments['currency'] }}">Currency</label></td>
          <td><input type="text" maxlength="3" class="form-control noformat" name="currency" value="{{ $ruleInfo->currency }}"></td>
        </tr>
        <tr>
          <td><label for="end_of_month" title="{{ $ruleComments['end_of_month'] }}">End of month</label></td>
          <td>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="end_of_month" value="1" {{ $ruleInfo->end_of_month ? 'checked' : '' }}>
              <label class="form-check-label" for="end_of_month">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input noformat" type="radio" name="end_of_month" value="0" {{ $ruleInfo->end_of_month ? '' : 'checked' }}>
              <label class="form-check-label" for="end_of_month">No</label>
            </div>
          </td>
        </tr>
      </table>
    </fieldset>
  </div>
</div>
