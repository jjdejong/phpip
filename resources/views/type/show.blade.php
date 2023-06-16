<div data-resource="/type/{{ $type->code }}">
	<table class="table table-hover table-sm">
		<tr>
			<th>{{ _i('Code') }}</td>
			<td><input class="noformat form-control" name="code" value="{{ $type->code }}"></td>
			<th><label title="{{ $tableComments['type'] }}">{{ _i('Name') }}</label></th>
			<td><input class="form-control noformat" name="type" value="{{ $type->type }}"></td>
		</tr>
	</table>
	<button type="button" class="btn btn-danger" title="{{ _i('Delete type') }}" id="deleteType" data-url='/type/{{ $type->code }}' data-message="the matter type {{ $type->type }}">
		{{ _i('Delete') }}
	</button>
</div>
