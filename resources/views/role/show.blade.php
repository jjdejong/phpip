<div data-resource="/role/{{ $role->code }}">
  <table class="table">
    <tr>
      <th>{{ _i('Code') }}</th>
      <td><input class="noformat form-control" name="code" value="{{ $role->code }}"></td>
      <th>{{ _i('Name') }}</th>
      <td><input class="form-control noformat" name="name" value="{{ $role->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['display_order'] }}">{{ _i('Display order') }}</label></th>
      <td><input class="form-control noformat" type='text' name="display_order" value="{{ $role->display_order }}">
      <th><label title="{{ $tableComments['shareable'] }}">{{ _i('Shareable') }}</label></th>
      <td><input type="checkbox" class="noformat" name="shareable" {{ $role->shareable ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_ref'] }}">{{ _i('Show reference') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_ref" {{ $role->show_ref ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_company'] }}">{{ _i('Show company') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_company" {{ $role->show_company ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_rate'] }}">{{ _i('Show rate') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_rate" {{ $role->show_rate ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_date'] }}">{{ _i('Show date') }}</label></th>
      <td><input type="checkbox" class="noformat" name="show_date" {{ $role->show_date ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <td><label title="{{ $tableComments['notes'] }}">{{ _i('Notes') }}</label></td>
      <td colspan=3><textarea class="form-control noformat" name="notes">{{ $role->notes }}</textarea></td>
    </tr>
  </table>
  <button type="button" class="btn btn-danger" title="{{ _i('Delete role') }}" id="deleteRole" data-message="{{ _i('the role ') . $role->name  }}" data-url='/role/{{ $role->code }}'>
    {{ _i('Delete') }}
  </button>
</div>
