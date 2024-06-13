<div data-resource="/type/{{ $type->code }}">
	<table class="table table-hover table-sm">
		<tr>
			<th>{{ __('Code') }}</td>
			<td><input class="noformat form-control" name="code" value="{{ $type->code }}"></td>
			<th><label title="{{ $tableComments['type'] }}">{{ __('Name') }}</label></th>
			<td><input class="form-control noformat" name="type" value="{{ $type->type }}"></td>
		</tr>
	</table>
	<button type="button" class="btn btn-danger" title="{{ __('Delete type') }}" id="deleteType" data-url='/type/{{ $type->code }}' data-message="{{ __('the matter type') }} {{ $type->type }}">
		{{ __('Delete') }}
	</button>
</div>
