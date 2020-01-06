<div data-resource="/type/{{ $type->code }}">
	<fieldset class="tab-pane" id="typeMain">
		<table class="table table-hover table-sm">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $type->code }}"></td>
				<td><label for="type" title="{{ $tableComments['type'] }}">Name</label></td>
				<td><input class="form-control noformat" name="type" value="{{ $type->type }}"></td>
			</tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete type" id="deleteType" data-url='/type/{{ $type->code }}' data-message="the matter type {{ $type->type }}">
			Delete
		</button>
	</fieldset>
</div>
