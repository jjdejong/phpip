<div data-resource="/dactor/{{ $dactor->id }}">
	<fieldset class="tab-pane" id="roleMain">
		<table class="table table-hover table-sm">
			<tr>
                <td><label title="{{ $tableComments['actor_id'] }}">Actor</label></td>
                <td><input type="text" class="noformat form-control" name="actor_id" data-ac="/actor/autocomplete" value="{{ empty($dactor->actor) ? '' : $dactor->actor->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['role'] }}">Role</label></td>
                <td><input type="text" class="noformat form-control" name="role" data-ac="/role/autocomplete" value="{{ empty($dactor->roleInfo) ? '' : $dactor->roleInfo->name }}" autocomplete="off"></td>
        </tr>
        <tr>
                <td><label title="{{ $tableComments['for_country'] }}">Country</label></td>
                <td><input type="text" class="noformat form-control" name="for_country" data-ac="/country/autocomplete" value="{{ empty($dactor->country) ? '' : $dactor->country->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['for_category'] }}">Category</label></td>
                <td><input type="text" class="noformat form-control" name="for_category" data-ac="/category/autocomplete" value="{{ empty($dactor->category) ? '' : $dactor->category->category }}" autocomplete="off"></td>
        </tr>
        <tr>
                <td><label title="{{ $tableComments['for_client'] }}">Client</label></td>
                <td><input type="text" class="noformat form-control" name="for_client" data-ac="/actor/autocomplete" value="{{ empty($dactor->client) ? '' : $dactor->client->name }}" autocomplete="off"></td>
                <td><label title="{{ $tableComments['shared'] }}">Is shared</label></td>
                <td><input type="checkbox" class="form-control noformat" name="shared" {{ $dactor->shared ? 'checked' : '' }}></td>
            </tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete entry" id="deleteDActor" data-dismiss="modal" data-id="{{ $dactor->id }}">
			Delete
		</button>
	</fieldset>
</div>
