<div data-resource="/category/{{ $categoryInfo->code }}">
	<fieldset class="tab-pane" id="categoryMain">
		<table class="table table-hover table-sm">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $categoryInfo->code }}"></td>
				<td><label for="category" title="{{ $tableComments['category'] }}">Category name</label></td>
				<td><input class="form-control noformat" name="category" value="{{ $categoryInfo->category }}"></td>
			</tr>
			<tr>
				<td><label for="ref_prefix" title="{{ $tableComments['ref_prefix'] }}">Reference prefix</label></td>
				<td><input class="form-control noformat" type='text' name="ref_prefix" value="{{ $categoryInfo->ref_prefix }}"></input>
				<td><label for="display_with" title="{{ $tableComments['display_with'] }}">Display with</label></td>
				<td><input type="text" class="form-control noformat" list="ajaxDatalist" data-ac="/category/autocomplete" name="display_with" value="{{ empty($categoryInfo->display_with) ? '' : $categoryInfo->displayWithInfo->category }}"></td>
			</tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete category" id="deleteCategory" data-dismiss="modal" data-id="{{ $categoryInfo->code }}">
			Delete
		</button>
	</fieldset>
</div>
