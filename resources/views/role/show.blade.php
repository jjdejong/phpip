<div data-resource="/role/{{ $role->code }}">
  <table class="table">
    <tr>
      <th>{{ __('Code') }}</th>
      <td><input class="noformat form-control" name="code" value="{{ $role->code }}"></td>
      <th>{{ __('Name') }}</th>
      <td><input class="form-control noformat" name="name" value="{{ $role->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['display_order'] }}">{{ __('Display order') }}</label></th>
      <td><input class="form-control noformat" type='text' name="display_order" value="{{ $role->display_order }}">
      <th><label title="{{ $tableComments['shareable'] }}">{{ __('Shareable') }}</label></th>
      <td><input type="checkbox" class="noformat" name="shareable" {{ $role->shareable ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_ref'] }}">{{ __('Show reference') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_ref" {{ $role->show_ref ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_company'] }}">{{ __('Show company') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_company" {{ $role->show_company ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_rate'] }}">{{ __('Show rate') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_rate" {{ $role->show_rate ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_date'] }}">{{ __('Show date') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_date" {{ $role->show_date ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <td><label title="{{ $tableComments['notes'] }}">{{ __('Notes') }}</label></td>
      <td colspan=3><textarea class="form-control noformat" name="notes">{{ $role->notes }}</textarea></td>
    </tr>
  </table>
  <button type="button" class="btn btn-danger" title="{{ __('Delete role') }}" id="deleteRole" data-message="{{ _i('the role ') . $role->name  }}" data-url='/role/{{ $role->code }}'>
    {{ __('Delete') }}
  </button>
</div>
