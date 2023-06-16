<div data-resource="/classifier_type/{{ $classifier_type->code }}">
	<table class="table">
		<tr>
			<th width="22%">{{ _i('Code') }}</th>
			<td width="20%"><input class="noformat form-control" name="code" value="{{ $classifier_type->code }}"></td>
			<th><label title="{{ $tableComments['type'] }}">{{ _i('Name') }}</label></th>
			<td><input class="form-control noformat" name="type" value="{{ $classifier_type->type }}"></td>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['display_order'] }}">{{ _i('Display order') }}</label></th>
			<td><input class="form-control noformat" type='text' name="display_order" value="{{ $classifier_type->display_order }}"></input>
			<th><label title="{{ $tableComments['for_category'] }}">{{ _i('Category') }}</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="for_category" value="{{ empty($classifier_type->for_category) ? '' : $classifier_type->category->category }}"></td>
		</tr>
		<tr>
      <th><label title="{{ $tableComments['main_display'] }}">{{ _i('Main display') }}</label></th>
      <td><input type="checkbox" class="noformat" name="main_display" {{ $classifier_type->main_display ? 'checked' : ''  }}></td>
      <th><label title="{{ $tableComments['notes'] }}">{{ _i('Notes') }}</label></th>
      <td><textarea class="form-control noformat" name="notes"> {{ $classifier_type->notes }}</textarea></td>
    </tr>
	</table>
	<button type="button" class="btn btn-danger" title="{{ _i('Delete type') }}" id="deleteClassifierType" data-message="type {{$classifier_type->type }}" data-url='/classifier_type/{{ $classifier_type->code }}'>
		{{ _i('Delete') }}
	</button>
</div>
