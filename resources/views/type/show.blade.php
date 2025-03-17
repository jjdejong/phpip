<div data-resource="/type/{{ $type->code }}">
    <div class="alert alert-info py-1 mb-2">
        <small>{{ __('Editing translations in') }}: 
            @if(Auth::user()->language == 'en' || explode('_', Auth::user()->language)[0] == 'en')
                English
            @elseif(Auth::user()->language == 'fr')
                Français
            @elseif(Auth::user()->language == 'de')
                Deutsch
            @elseif(Auth::user()->language == 'es')
                Español
            @else
                {{ Auth::user()->language }}
            @endif
        </small>
    </div>
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
