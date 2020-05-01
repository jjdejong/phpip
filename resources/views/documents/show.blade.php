<div data-resource="/document/{{ $class->id }}">
  <table class="table">
    <tr>
      <th>Name</th>
      <td><input class="form-control noformat" name="name" value="{{ $class->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['description'] }}">Description</label></th>
      <td><input class="form-control noformat" type='text' name="description" value="{{ $class->description }}">
    </tr>
    <tr>
      <th><label title="{{ $tableComments['category_id'] }}">Category</label></th>
      <td><input type="text" class="form-control noformat" name="category_id" data-ac="/template-category/autocomplete" value="{{ is_null($class->category) ? "" : $class->category->category }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['default_role'] }}">Role</label></th>
      <td><input type="text" class="noformat" name="default_role" data-ac="/role/autocomplete" value="{{ is_null($class->role) ? "" : $class->role->name }}"</td>
    </tr>
    @if (count($class->eventNames) != 0)
    <tr>
      <th><label>Event names</label></th>  <td>
      @foreach ($class->eventNames as $link)
        {{ $link->name }}<br>
      @endforeach
    </td></tr>
    @endif
    @if (count($class->rules) != 0)
    <tr>
      <th><label>Rules</label></th>
      <td>
      @foreach ($class->rules as $link)
        {{ $link->taskInfo->name }}<br>
      @endforeach
      </td>
    </tr>
    @endif
  </table>
  <button type="button" class="btn btn-danger" title="Delete class" id="deleteClass" data-message="the class {{ $class->name  }}" data-url='/document/{{ $class->id }}'>
    Delete
  </button>
</div>
