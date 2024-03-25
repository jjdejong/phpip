<form id="createEventForm">
  <div class="row mb-2">
    <div class="col">
      <label for="code" title="{{ __($tableComments['code']) }}"><b>{{ __('Code') }}</b></label>
      <input type="text" class="form-control" name="code">
    </div>
    <div class="col p-2 btn-group">
      <input class="btn-check" type="radio" id="btn-notask" name="is_task" value="0" checked>
      <label class="btn btn-outline-primary w-25" for="btn-notask">{{ __('Not Task') }}</label>
      <input class="btn-check" type="radio" id="btn-istask" name="is_task" value="1">
      <label class="btn btn-outline-primary" for="btn-istask" title="{{ __($tableComments['is_task']) }}">{{ __('Task') }}</label>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col">
      <label for="name" title="{{ __($tableComments['name']) }}"><b>{{ __('Name') }}</b></label>
      <input type="text" class="form-control" name="name">
    </div>
    <div class="col p-2 btn-group">
      <input class="btn-check" type="radio" id="btn-nostatus" name="status_event" value="0" checked>
      <label class="btn btn-outline-primary" for="btn-nostatus">{{ _('Normal') }}</label>
      <input class="btn-check" type="radio" id="btn-isstatus" name="status_event" value="1">
      <label class="btn btn-outline-primary" for="btn-isstatus" title="{{ __($tableComments['status_event']) }}">{{ __('Status') }}</label>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col">
      <label title="{{ __($tableComments['default_responsible']) }}">{{ __('Default responsible') }}</label>
      <input type='hidden' name='default_responsible'>
      <input type="text" class="form-control" data-ac="/user/autocomplete" data-actarget="default_responsible"
        autocomplete="off">
    </div>
    <div class="col">
      <label title="{{ __($tableComments['use_matter_resp']) }}">{{ __('Use matter responsible') }}</label>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="use_matter_resp" value="1">
        <label class="form-check-label">{{ __('Yes') }}</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="use_matter_resp" value="0" checked>
        <label class="form-check-label">{{ __('No') }}</label>
      </div>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col">
      <label for="country" title="{{ __($tableComments['country']) }}">{{ __('Country') }}</label>
      <input type='hidden' name='country'>
      <input type="text" class="form-control" data-ac="/country/autocomplete" data-actarget="country"
        autocomplete="off">
    </div>
    <div class="col">
      <label for="category" title="{{ __($tableComments['category']) }}">{{ __('Category') }}</label>
      <input type='hidden' name='category'>
      <input type="text" class="form-control" data-ac="/category/autocomplete" data-actarget="category"
        autocomplete="off">
    </div>
  </div>
  <div class="row mb-2">
    <div class="col">
      <label for="notes" title="{{ __($tableComments['notes']) }}">{{ __('Notes') }}</label>
      <textarea class="form-control" name="notes"></textarea>
    </div>
    <div class="col">
      <label title="{{ $tableComments['killer'] }}">{{ __('Is killer') }}</label>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="killer" value="1">
        <label class="form-check-label">{{ __('Yes') }}</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="killer" value="0" checked>
        <label class="form-check-label">{{ __('No') }}</label>
      </div>
    </div>
  </div>
  <div class="d-grid">
    <button type="button" id="createEventNameSubmit" class="btn btn-primary">{{ __('Create event name') }}</button>
  </div>
</form>
