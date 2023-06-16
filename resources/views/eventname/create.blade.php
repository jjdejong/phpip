<form id="createEventForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>{{ _i("Code") }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label title="{{ $tableComments['is_task'] }}">{{ _i("Is task") }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_task" value="1">
            <label class="form-check-label">{{ _i("Yes") }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_task" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="name" title="{{ $tableComments['name'] }}"><b>{{ _i("Name") }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="name"></td>
        <td><label title="{{ $tableComments['status_event'] }}">{{ _i("Is status event") }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status_event" value="1">
            <label class="form-check-label">{{ _i("Yes") }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status_event" value="0" checked>
            <label class="form-check-label">{{ _i("No") }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label title="{{ $tableComments['default_responsible'] }}">{{ _i("Default responsible") }}</label>
        </td>
        <td>
          <input type='hidden' name='default_responsible'>
          <input type="text" class="form-control form-control-sm" data-ac="/user/autocomplete" data-actarget="default_responsible" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['use_matter_resp'] }}">{{ _i("Use matter responsible") }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_matter_resp" value="1">
            <label class="form-check-label">{{ _i("Yes") }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_matter_resp" value="0" checked>
            <label class="form-check-label">{{ _i("No") }}</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="country" title="{{ $tableComments['country'] }}">{{ _i("Country") }}</label></td>
        <td>
          <input type='hidden' name='country'>
          <input type="text" class="form-control form-control-sm" data-ac="/country/autocomplete" data-actarget="country" autocomplete="off">
        </td>
        <td colspan="2"></td>
      <tr>
        <td><label for="category" title="{{ $tableComments['category'] }}">{{ _i("Category") }}</label></td><td>
          <input type='hidden' name='category'>
          <input type="text" class="form-control form-control-sm" data-ac="/category/autocomplete" data-actarget="category" autocomplete="off">
        </td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td><label for="notes" title="{{ $tableComments['notes'] }}">{{ _i("Notes") }}</label></td>
        <td><textarea class="form-control form-control-sm" name="notes"></textarea></td>
        <td><label title="{{ $tableComments['killer'] }}">{{ _i("Is killer") }}</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="killer" value="1">
            <label class="form-check-label">{{ _i('Yes') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="killer" value="0" checked>
            <label class="form-check-label">{{ _i('No') }}</label>
          </div>
        </td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createEventNameSubmit" class="btn btn-primary">{{ _i("Create event name") }}</button><br>
</form>
