<form id="natMatterForm">
	<input type="hidden" name="caseref" value="{{ $parent_matter->caseref }}" />
	<input type="hidden" name="category_code" value="{{ $parent_matter->category_code }}" />
	<input type="hidden" name="origin" value="{{ $parent_matter->country }}" />
	<input type="hidden" name="type_code" value="{{ $parent_matter->type_code }}" />
	<input type="hidden" name="idx" value="{{ $parent_matter->idx }}" />
	<input type="hidden" name="parent_id" value="{{ $parent_matter->id }}" />
	<input type="hidden" name="responsible" value="{{ $parent_matter->responsible }}" />
	<div id="ncountries">
		@foreach( $parent_matter->countryInfo->natcountries as $iso => $name )
		<div class="input-group" id="country-{{ $iso }}">
			<input type="hidden" name="ncountry[]" value="{{ $iso }}" />
			<input type="text" class="form-control" readonly value="{{ $name }}" />
			<button class="btn btn-outline-danger" type="button" id="{{ $iso }}" title="Remove {{ $iso }}">&times;</button>
		</div>
		@endforeach
	</div>
	<div class="input-group">
		<input type="text" class="form-control" placeholder="Add country" data-ac="/country/autocomplete" id="addCountry">
		<span class="input-group-text">&plus;</span>
	</div>
	<button type="button" class="btn btn-primary mt-2" id="nationalizeSubmit">Submit</button>
</form>

<template id="appendCountryTemplate">
	<div class="input-group">
		<input type="hidden" name="ncountry[]">
		<input type="text" class="form-control" value="" readonly>
		<button class="btn btn-outline-danger" type="button">&times;</button>
	</div>
</template>
