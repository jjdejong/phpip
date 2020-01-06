<div data-resource="/classifier_type/{{ $classifier_type->code }}">
	<fieldset class="tab-pane" id="categoryMain">
		<table class="table table-hover table-sm">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $classifier_type->code }}"></td>
				<td><label for="type" title="{{ $tableComments['type'] }}">Type</label></td>
				<td><input class="form-control noformat" name="type" value="{{ $classifier_type->type }}"></td>
			</tr>
			<tr>
				<td><label for="display_order" title="{{ $tableComments['display_order'] }}">Display order</label></td>
				<td><input class="form-control noformat" type='text' name="display_order" value="{{ $classifier_type->display_order }}"></input>
				<td><label for="for_category" title="{{ $tableComments['for_category'] }}">Category</label></td>
				<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="for_category" value="{{ empty($classifier_type->for_category) ? '' : $classifier_type->category->category }}"></td>
			</tr>
			<tr>
                <td><label for="main_display" title="{{ $tableComments['main_display'] }}">Main display</label></td>
                <td><input type="checkbox" class="form-control form-control-sm noformat" name="main_display" {{ $classifier_type->main_display ? 'checked' : ''  }}></td>
                <td><label for="type" title="{{ $tableComments['notes'] }}">Notes</label></td>
                <td><textarea class="form-control form-control-sm noformat" name="notes"> {{ $classifier_type->notes }}</textarea></td>
            </tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete type" id="deleteClassifierType" data-message="type {{$classifier_type->type }}" data-url='/classifier_type/{{ $classifier_type->code }}'>
			Delete
		</button>
	</fieldset>
</div>
