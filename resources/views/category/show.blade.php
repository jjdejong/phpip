<div data-resource="/category/{{ $category->code }}" class="position-relative" style="padding-bottom: 50px;">
	<table class="table">
		<tr>
			<th width="20%">{{ __('Code') }}</th>
			<td width="20%"><input class="noformat form-control" name="code" value="{{ $category->code }}"></td>
			<th><label title="{{ $tableComments['category'] }}">{{ __('Name') }}</label></th>
			<td><input class="form-control noformat" name="category" value="{{ $category->category }}"></td>
		</tr>
		<tr>
			<th><label title="{{ $tableComments['ref_prefix'] }}">{{ __('Prefix') }}</label></th>
			<td><input class="form-control noformat" type='text' name="ref_prefix" value="{{ $category->ref_prefix }}"></input>
			<th><label title="{{ $tableComments['display_with'] }}">{{ __('Display with') }}</label></th>
			<td><input type="text" class="form-control noformat" data-ac="/category/autocomplete" name="display_with" value="{{ empty($category->display_with) ? '' : $category->displayWithInfo->category }}"></td>
		</tr>
	</table>
	<button type="button" class="btn btn-outline-danger btn-sm position-absolute" title="{{ __('Delete category') }}" id="deleteCategory" data-message="{{ __('category') }} {{$category->category }}" data-url='/category/{{ $category->code }}' style="bottom: 10px; right: 10px;">
		<svg width="16" height="16" fill="currentColor" class="me-1">
			<use xlink:href="#trash"/>
		</svg>
		{{ __('Delete') }}
	</button>
</div>
