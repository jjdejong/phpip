<div data-resource="/document/{{ $class->id }}">
  <table class="table">
    <tr>
      <th>{{ _i("Name") }}</th>
      <td><input class="form-control noformat" name="name" value="{{ $class->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['notes'] }}">{{ _i("Notes") }}</label></th>
      <td><input class="form-control noformat" type='text' name="notes" value="{{ $class->notes }}">
    </tr>
    <tr>
      <th><label title="{{ $tableComments['default_role'] }}">{{ _i("Role") }}</label></th>
      <td><input type="text" class="noformat" name="default_role" data-ac="/role/autocomplete" value="{{ is_null($class->role) ? "" : $class->role->name }}"</td>
    </tr>
    @if (count($class->eventNames) != 0)
    <tr>
      <th><label>{{ _i('Event names linked to this class') }}</label></th>  <td>
      @foreach ($class->eventNames as $link)
        {{ $link->name }}<br>
      @endforeach
    </td></tr>
    @endif
  </table>
  <button type="button" class="btn btn-danger" title="{{ _i('Delete class') }}" id="deleteClass" data-message="{{ _i('the class ') }}{{ $class->name  }}" data-url='/document/{{ $class->id }}'>
    {{ _i("Delete") }}
  </button>
</div>
