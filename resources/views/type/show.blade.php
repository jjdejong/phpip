<div data-resource="/type/{{ $type->code }}">
	<table class="table table-hover table-sm">
		<tr>
			<th>Code</td>
			<td><input class="noformat form-control" name="code" value="{{ $type->code }}"></td>
			<th><label title="{{ $tableComments['type'] }}">Name</label></th>
			<td><input class="form-control noformat" name="type" value="{{ $type->type }}"></td>
		</tr>
	</table>
	<button type="button" class="btn btn-danger" title="Delete type" id="deleteType" data-url='/type/{{ $type->code }}' data-message="the matter type {{ $type->type }}">
		Delete
	</button>
</div>
