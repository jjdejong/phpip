<form id="createEventForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>Code</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label title="{{ $tableComments['is_task'] }}">Is task</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_task" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_task" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="name" title="{{ $tableComments['name'] }}"><b>Name</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="name"></td>
        <td><label title="{{ $tableComments['status_event'] }}">Is status event</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status_event" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status_event" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label title="{{ $tableComments['default_responsible'] }}">Default responsible</label>
        </td>
        <td>
          <input type='hidden' name='default_responsible'>
          <input type="text" class="form-control form-control-sm" data-ac="/user/autocomplete" data-actarget="default_responsible" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['use_matter_resp'] }}">Use matter responsible</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_matter_resp" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="use_matter_resp" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="country" title="{{ $tableComments['country'] }}">Country</label></td>
        <td>
          <input type='hidden' name='country'>
          <input type="text" class="form-control form-control-sm" data-ac="/country/autocomplete" data-actarget="country" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['unique'] }}">Is unique</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="unique" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="unique" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      <tr>
        <td><label for="category" title="{{ $tableComments['category'] }}">Category</label></td><td>
          <input type='hidden' name='category'>
          <input type="text" class="form-control form-control-sm" data-ac="/category/autocomplete" data-actarget="category" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['uqtrigger'] }}">Unique trigger</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="uqtrigger" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="uqtrigger" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      </tr>
      <tr>
        <td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label></td>
        <td><textarea class="form-control form-control-sm" name="notes"></textarea></td>
        <td><label title="{{ $tableComments['killer'] }}">Is killer</label></td>
        <td>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="killer" value="1">
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="killer" value="0" checked>
            <label class="form-check-label">No</label>
          </div>
        </td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createEventNameSubmit" class="btn btn-primary">Create event name</button><br>
  <span id="zoneAlert" class="alert float-left"></span>
</form>
