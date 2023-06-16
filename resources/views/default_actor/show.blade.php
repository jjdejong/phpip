<div data-resource="/default_actor/{{ $default_actor->id }}">
  <table class="table">
    <tr>
      <th width="15%"><label title="{{ $tableComments['actor_id'] }}">{{ _i('Actor') }}</label></th>
      <td><input type="text" class="noformat form-control" name="actor_id" data-ac="/actor/autocomplete" value="{{ empty($default_actor->actor) ? '' : $default_actor->actor->name }}" autocomplete="off"></td>
      <th><label title="{{ $tableComments['role'] }}">{{ _i('Role') }}</label></th>
      <td><input type="text" class="noformat form-control" name="role" data-ac="/role/autocomplete" value="{{ empty($default_actor->roleInfo) ? '' : $default_actor->roleInfo->name }}" autocomplete="off"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['for_country'] }}">{{ _i('Country') }}</label></th>
      <td><input type="text" class="noformat form-control" name="for_country" data-ac="/country/autocomplete" value="{{ empty($default_actor->country) ? '' : $default_actor->country->name }}" autocomplete="off"></td>
      <th><label title="{{ $tableComments['for_category'] }}">{{ _i('Category') }}</label></th>
      <td><input type="text" class="noformat form-control" name="for_category" data-ac="/category/autocomplete" value="{{ empty($default_actor->category) ? '' : $default_actor->category->category }}" autocomplete="off"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['for_client'] }}">Client</label></th>
      <td><input type="text" class="noformat form-control" name="for_client" data-ac="/actor/autocomplete" value="{{ empty($default_actor->client) ? '' : $default_actor->client->name }}" autocomplete="off"></td>
      <th><label title="{{ $tableComments['shared'] }}">{{ _i('Shared') }}</label></th>
      <td><input type="checkbox" class="noformat" name="shared" {{ $default_actor->shared ? 'checked' : '' }}></td>
    </tr>
  </table>
  <button type="button" class="btn btn-danger" title="{{ _i('Delete entry') }}" id="deleteDActor" data-message="entry for {{ $default_actor->actor->name  }}" data-url='/default_actor/{{ $default_actor->id }}'>
    {{ _i('Delete') }}
  </button>
</div>
