<form id="natMatterForm" class="ui-front">
	<input type="hidden" name="caseref" value="{{ $from_matter->caseref }}" />
	<input type="hidden" name="category_code" value="{{ $from_matter->category_code }}" />
	<input type="hidden" name="origin" value="{{ $from_matter->country }}" />
	<input type="hidden" name="type_code" value="{{ $from_matter->type_code }}" />
	<input type="hidden" name="idx" value="{{ $from_matter->idx }}" />
	<input type="hidden" name="origin_id" value="{{ $from_matter->id }}" />
	<input type="hidden" name="responsible" value="{{ $from_matter->responsible }}" />
	<div id="ncountries">
		@foreach( $from_matter->countryInfo->natcountries as $iso => $name )
		<div class="input-group" id="country-{{ $iso }}">
			<input type="hidden" name="ncountry[]" value="{{ $iso }}" />
			<input type="text" class="form-control" readonly value="{{ $name }}" />
			<div class="input-group-append">
				<button class="btn btn-outline-danger" type="button" id="{{ $iso }}" title="Remove {{ $iso }}">&times;</button>
			</div>
		</div>
		@endforeach
	</div>
	<div class="input-group">
		<input type="text" class="form-control" placeholder="Add country" data-ac="/country/autocomplete" id="addCountry">
		<div class="input-group-append">
			<span class="input-group-text">&plus;</span>
		</div>
	</div>
	<div id="add-matter-actions">
		<button type="button" class="btn btn-primary" id="nationalizeSubmit">Submit</button>
	</div>
</form>

<template id="appendCountryTemplate">
	<div class="input-group">
		<input type="hidden" name="ncountry[]">
		<input type="text" class="form-control" value="" readonly>
		<div class="input-group-append">
			<button class="btn btn-outline-danger" type="button">&times;</button>
		</div>
	</div>
</template>
