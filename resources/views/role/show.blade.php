<div data-resource="/role/{{ $role->code }}">
    <div class="alert alert-info py-1 mb-2">
        <small>{{ __('Editing translations in') }}: 
            @if(Auth::user()->language == 'en' || explode('_', Auth::user()->language)[0] == 'en')
                English
            @elseif(Auth::user()->language == 'fr')
                Français
            @elseif(Auth::user()->language == 'de')
                Deutsch
            @elseif(Auth::user()->language == 'es')
                Español
            @else
                {{ Auth::user()->language }}
            @endif
        </small>
    </div>
  <table class="table">
    <tr>
      <th>Code</th>
      <td><input class="noformat form-control" name="code" value="{{ $role->code }}"></td>
      <th>Name</th>
      <td><input class="form-control noformat" name="name" value="{{ $role->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['display_order'] }}">Display order</label></th>
      <td><input class="form-control noformat" type='text' name="display_order" value="{{ $role->display_order }}">
      <th><label title="{{ $tableComments['shareable'] }}">Shareable</label></th>
      <td><input type="checkbox" class="noformat" name="shareable" {{ $role->shareable ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_ref'] }}">Show reference</label></th>
      <td><input type="checkbox" class="noformat" name="show_ref" {{ $role->show_ref ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_company'] }}">Show company</label></th>
      <td><input type="checkbox" class="noformat" name="show_company" {{ $role->show_company ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['show_rate'] }}">Show rate</label></th>
      <td><input type="checkbox" class="noformat" name="show_rate" {{ $role->show_rate ? 'checked' : '' }}></td>
      <th><label title="{{ $tableComments['show_date'] }}">Show date</label></th>
      <td><input type="checkbox" class="noformat" name="show_date" {{ $role->show_date ? 'checked' : '' }}></td>
    </tr>
    <tr>
      <td><label title="{{ $tableComments['notes'] }}">Notes</label></td>
      <td colspan=3><textarea class="form-control noformat" name="notes">{{ $role->notes }}</textarea></td>
    </tr>
  </table>
  <button type="button" class="btn btn-danger" title="Delete role" id="deleteRole" data-message="the role {{ $role->name  }}" data-url='/role/{{ $role->code }}'>
    Delete
  </button>
</div>
