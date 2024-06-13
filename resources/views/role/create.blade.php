<form id="createRoleForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>{{ __('Code') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="name" title="{{ $tableComments['name'] }}"><b>{{ __('Role') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="name"></td>
      </tr>
      <tr>
        <td><label for="display_order" title="{{ $tableComments['display_order'] }}">{{ __('Display order') }}</label></td>
        <td><input type="text" class="form-control form-control-sm" name="display_order"></input></td>
        <td><label title="{{ $tableComments['shareable'] }}">{{ __('Is shareable') }}</label></td>
        <td><input type="checkbox" class="form-control form-control-sm" value="1" name="shareable"></td>
      </tr>
      <tr>
        <td><label for="show_ref" title="{{ $tableComments['show_ref'] }}">{{ __('Show reference') }}</label></td>
        <td><input type="checkbox" class="form-control form-control-sm" value="1" name="show_ref"></td>
        <td><label for="show_company" title="{{ $tableComments['show_company'] }}">{{ __('Show company') }}</label></td>
        <td><input type="checkbox" class="form-control form-control-sm" value="1" name="show_company"></td>
      </tr>
      <tr>
        <td><label for="show_rate" title="{{ $tableComments['show_rate'] }}">{{ __('Show rate') }}</label></td>
        <td><input type="checkbox" class="form-control form-control-sm" value="1" name="show_rate"></td>
        <td><label for="show_date" title="{{ $tableComments['show_date'] }}">{{ __('Show date') }}</label></td>
        <td><input type="checkbox" class="form-control form-control-sm" value="1" name="show_date"></td>
      </tr>
      <tr>
        <td><label for="notes" title="{{ $tableComments['notes'] }}">{{ __('Notes') }}</label></td>
        <td colspan=3><textarea class="form-control form-control-sm" name="notes"></textarea></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createRoleSubmit" class="btn btn-primary">{{ __('Create role') }}</button><br>
</form>
