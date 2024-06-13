<div data-resource="/document/{{ $class->id }}">
  <table class="table">
    <tr>
      <th>{{ __("Name") }}</th>
      <td><input class="form-control noformat" name="name" value="{{ $class->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ __($tableComments['notes']) }}">{{ __("Notes") }}</label></th>
      <td><input class="form-control noformat" type='text' name="notes" value="{{ $class->notes }}">
    </tr>
    <tr>
      <th><label title="{{ __($tableComments['default_role']) }}">{{ __("Role") }}</label></th>
      <td><input type="text" class="noformat" name="default_role" data-ac="/role/autocomplete" value="{{ is_null($class->role) ? "" : $class->role->name }}"</td>
    </tr>
    @if (count($class->eventNames) != 0)
    <tr>
      <th><label>{{ __('Event names linked to this class') }}</label></th>  <td>
      @foreach ($class->eventNames as $link)
        {{ $link->name }}<br>
      @endforeach
    </td></tr>
    @endif
  </table>
  <button type="button" class="btn btn-danger" title="{{ __('Delete class') }}" id="deleteClass" data-message="{{ __('the class ') }}{{ $class->name  }}" data-url='/document/{{ $class->id }}'>
    {{ __("Delete") }}
  </button>
</div>
