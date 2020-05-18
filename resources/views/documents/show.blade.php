<div data-resource="/document/{{ $class->id }}">
  <table class="table">
    <tr>
      <th>Name</th>
      <td><input class="form-control noformat" name="name" value="{{ $class->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['notes'] }}">Notes</label></th>
      <td><input class="form-control noformat" type='text' name="notes" value="{{ $class->notes }}">
    </tr>
    <tr>
      <th><label title="{{ $tableComments['default_role'] }}">Role</label></th>
      <td><input type="text" class="noformat" name="default_role" data-ac="/role/autocomplete" value="{{ is_null($class->role) ? "" : $class->role->name }}"</td>
    </tr>
    @if (count($class->eventNames) != 0)
    <tr>
      <th><label>Event names linked to this class</label></th>  <td>
      @foreach ($class->eventNames as $link)
        {{ $link->name }}<br>
      @endforeach
    </td></tr>
    @endif
  </table>
  <button type="button" class="btn btn-danger" title="Delete class" id="deleteClass" data-message="the class {{ $class->name  }}" data-url='/document/{{ $class->id }}'>
    Delete
  </button>
</div>
