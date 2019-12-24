<div data-resource="/default_actor/{{ $default_actor->id }}">
	<fieldset class="tab-pane" id="roleMain">
		<table class="table table-hover table-sm">
			<tr>
                <td><label title="{{ $tableComments['actor_id'] }}">Actor</label></td>
                <td><input type="text" class="noformat form-control" name="actor_id" data-ac="/actor/autocomplete" value="{{ empty($default_actor->actor) ? '' : $default_actor->actor->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['role'] }}">Role</label></td>
                <td><input type="text" class="noformat form-control" name="role" data-ac="/role/autocomplete" value="{{ empty($default_actor->roleInfo) ? '' : $default_actor->roleInfo->name }}" autocomplete="off"></td>
        </tr>
        <tr>
                <td><label title="{{ $tableComments['for_country'] }}">Country</label></td>
                <td><input type="text" class="noformat form-control" name="for_country" data-ac="/country/autocomplete" value="{{ empty($default_actor->country) ? '' : $default_actor->country->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['for_category'] }}">Category</label></td>
                <td><input type="text" class="noformat form-control" name="for_category" data-ac="/category/autocomplete" value="{{ empty($default_actor->category) ? '' : $default_actor->category->category }}" autocomplete="off"></td>
        </tr>
        <tr>
                <td><label title="{{ $tableComments['for_client'] }}">Client</label></td>
                <td><input type="text" class="noformat form-control" name="for_client" data-ac="/actor/autocomplete" value="{{ empty($default_actor->client) ? '' : $default_actor->client->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['shared'] }}">Is shared</label></td>
                <td><input type="checkbox" class="form-control noformat" name="shared" {{ $default_actor->shared ? 'checked' : '' }}></td>
            </tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete entry" id="deleteDActor" data-dismiss="modal" data-id="{{ $default_actor->id }}">
			Delete
		</button>
	</fieldset>
</div>
