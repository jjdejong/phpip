<form id="createDActorForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
            <td><label title="{{ $tableComments['actor_id'] }}">{{ _i('Actor') }}</label></td>
            <td><input type="hidden" name="actor_id">
            <input type="text" class="form-control form-control-sm" placeholder="{{ _i('Actor') }}" data-ac="/actor/autocomplete" data-actarget="actor_id"></td>
            <td><label title="{{ $tableComments['role'] }}">{{ _i('Role') }}</label></td>
            <td><input type="hidden" name="role">
          <input type="text" class="form-control form-control-sm" placeholder="{{ _i('Role') }}" data-ac="/role/autocomplete" data-actarget="role"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_country'] }}">{{ _i('Country') }}</label></td>
            <td><input type="hidden" name="for_country">
            <input type="text" class="form-control form-control-sm" placeholder="{{ _i('Country') }}" data-ac="/country/autocomplete" data-actarget="for_country"></td>
            <td><label title="{{ $tableComments['for_category'] }}">{{ _i('Category') }}</label></td>
            <td><input type="hidden" name="for_category">
          <input type="text" class="form-control form-control-sm" placeholder="{{ _i('Category') }}" data-ac="/category/autocomplete" data-actarget="for_category"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_client'] }}">{{ _i('Client') }}</label></td>
            <td><input type="hidden" name="for_client">
          <input type="text" class="form-control form-control-sm" placeholder="{{ _i('Client') }}" data-ac="/actor/autocomplete" data-actarget="for_client"></td>
            <td><label title="{{ $tableComments['shared'] }}">{{ _i('Is shared') }}</label></td>
          <td><input type="checkbox" class="form-control form-control-sm" value="1" name="shared"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createDActorSubmit" class="btn btn-primary">{{ _i('Create entry') }}</button><br>
</form>
