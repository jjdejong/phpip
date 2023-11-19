<form id="createDActorForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
            <td><label title="{{ $tableComments['actor_id'] }}">{{ __('Actor') }}</label></td>
            <td><input type="hidden" name="actor_id">
            <input type="text" class="form-control form-control-sm" placeholder="{{ __('Actor') }}" data-ac="/actor/autocomplete" data-actarget="actor_id"></td>
            <td><label title="{{ $tableComments['role'] }}">{{ __('Role') }}</label></td>
            <td><input type="hidden" name="role">
          <input type="text" class="form-control form-control-sm" placeholder="{{ __('Role') }}" data-ac="/role/autocomplete" data-actarget="role"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_country'] }}">{{ __('Country') }}</label></td>
            <td><input type="hidden" name="for_country">
            <input type="text" class="form-control form-control-sm" placeholder="{{ __('Country') }}" data-ac="/country/autocomplete" data-actarget="for_country"></td>
            <td><label title="{{ $tableComments['for_category'] }}">{{ __('Category') }}</label></td>
            <td><input type="hidden" name="for_category">
          <input type="text" class="form-control form-control-sm" placeholder="{{ __('Category') }}" data-ac="/category/autocomplete" data-actarget="for_category"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_client'] }}">{{ __('Client') }}</label></td>
            <td><input type="hidden" name="for_client">
          <input type="text" class="form-control form-control-sm" placeholder="{{ __('Client') }}" data-ac="/actor/autocomplete" data-actarget="for_client"></td>
            <td><label title="{{ $tableComments['shared'] }}">{{ __('Is shared') }}</label></td>
          <td><input type="checkbox" class="form-control form-control-sm" value="1" name="shared"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createDActorSubmit" class="btn btn-primary">{{ __('Create entry') }}</button><br>
</form>
