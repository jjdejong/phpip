<div data-resource="/category/{{ $category->code }}">
	<table class="table">
		<tr>
			<th width="20%">{{ __("Code") }}</th>
			<td width="20%"><input class="noformat form-control" name="code" value="{{ $category->code }}"></td>
			<th><label title="{{ $tableComments['category'] }}">{{ __("Name") }}</label></th>
			<td><input class="form-control noformat" name="category" value="{{ $category->category }}"></td>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['ref_prefix'] }}">{{ __("Prefix") }}</label></th>
			<td><input class="form-control noformat" type='text' name="ref_prefix" value="{{ $category->ref_prefix }}"></input>
			<th><label title="{{ $tableComments['display_with'] }}">{{ __("Display with") }}</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="display_with" value="{{ empty($category->display_with) ? '' : $category->displayWithInfo->category }}"></td>
		</tr>
	</table>
	<button type="button" class="btn btn-danger" title="Delete category" id="deleteCategory" data-message="category {{$category->category }}" data-url='/category/{{ $category->code }}'>
		{{ __("Delete") }}
	</button>
</div>
