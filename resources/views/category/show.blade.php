<div data-resource="/category/{{ $category->code }}">
	<fieldset class="tab-pane" id="categoryMain">
		<table class="table table-hover table-sm">
			<tr>
				<td><label for="code">Code</label></td>
				<td><input class="noformat form-control" name="code" value="{{ $category->code }}"></td>
				<td><label for="category" title="{{ $tableComments['category'] }}">Name</label></td>
				<td><input class="form-control noformat" name="category" value="{{ $category->category }}"></td>
			</tr>
			<tr>
				<td><label for="ref_prefix" title="{{ $tableComments['ref_prefix'] }}">Reference prefix</label></td>
				<td><input class="form-control noformat" type='text' name="ref_prefix" value="{{ $category->ref_prefix }}"></input>
				<td><label for="display_with" title="{{ $tableComments['display_with'] }}">Display with</label></td>
				<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="display_with" value="{{ empty($category->display_with) ? '' : $category->displayWithInfo->category }}"></td>
			</tr>
		</table>
		<button type="button" class="btn btn-danger" title="Delete category" id="deleteCategory" data-dismiss="modal" data-id="{{ $category->code }}">
			Delete
		</button>
	</fieldset>
</div>
