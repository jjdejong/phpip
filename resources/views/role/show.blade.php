<div data-resource="/role/{{ $role->code }}">
	<fieldset class="tab-pane" id="roleMain">
		<table class="table table-hover table-sm">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $role->code }}"></td>
				<td><label for="name" title="{{ $tableComments['name'] }}">Role</label></td>
				<td><input class="form-control noformat" name="name" value="{{ $role->name }}"></td>
			</tr>
			<tr>
				<td><label for="display_order" title="{{ $tableComments['display_order'] }}">Display order</label></td>
				<td><input class="form-control noformat" type='text' name="display_order" value="{{ $role->display_order }}">
				<td><label for="shareable" title="{{ $tableComments['shareable'] }}">Shareable</label></td></input></td>
				<td><input type="checkbox" class="form-control noformat" name="shareable" {{ $role->shareable ? 'checked' : '' }}></td>
			</tr>
			<tr>
				<td><label for="show_ref" title="{{ $tableComments['show_ref'] }}">Show reference</label></td></input></td>
				<td><input type="checkbox" class="form-control noformat" name="show_ref" {{ $role->show_ref ? 'checked' : '' }}></td>
				<td><label for="show_company" title="{{ $tableComments['show_company'] }}">Show company</label></td></input></td>
				<td><input type="checkbox" class="form-control noformat" name="show_company" {{ $role->show_company ? 'checked' : '' }}></td>
			</tr>
			<tr>
				<td><label for="show_rate" title="{{ $tableComments['show_rate'] }}">Show rate</label></td></input></td>
				<td><input type="checkbox" class="form-control noformat" name="show_rate" {{ $role->show_rate ? 'checked' : '' }}></td>
				<td><label for="show_date" title="{{ $tableComments['show_date'] }}">Show date</label></td></input></td>
				<td><input type="checkbox" class="form-control noformat" name="show_date" {{ $role->show_date ? 'checked' : '' }}></td>
			</tr>
            <tr>
                <td><label for="notes" title="{{ $tableComments['notes'] }}">Notes</label><br /></td>
                <td colspan=3><textarea class="form-control noformat" name="notes">{{ $role->notes }}</textarea></td>
            </tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete role" id="deleteRole" data-message="the role {{ $role->name  }}" data-url='/role/{{ $role->code }}'>
			Delete
		</button>
	</fieldset>
</div>
