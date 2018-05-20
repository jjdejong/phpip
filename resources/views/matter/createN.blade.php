{{-- <form id="nationalizeMatterForm" class="ui-front">
  <div class="form-group row">
    <label for="category" class="col-3 col-form-label font-weight-bold">Category</label>
    <div class="col-9">
      <input type="hidden" name="category_code" value="{{ $matter->category_code }}" />
      <input type="text" class="form-control" id="category" value="{{ $matter->category->category }}" readonly />
    </div>
  </div>
  <div class="form-group row">
    <label for="country" class="col-3 col-form-label font-weight-bold">Country</label>
    <div class="col-9">
      <input type="hidden" name="country" value="{{ $matter->country }}" />
      <input type="text" class="form-control" id="country" value="{{ $matter->countryInfo->name }}" onFocus="this.select()" />
    </div>
  </div>
  <div class="form-group row">
    <label for="origin" class="col-3 col-form-label">Origin</label>
    <div class="col-9">
      <input type="hidden" name="origin" value="{{ $matter->country }}" />
      <input type="text" class="form-control" id="origin" value="{{ $matter->countryInfo->name }}" readonly />
    </div>
  </div>
  <div class="form-group row">
    <label for="type_code" class="col-3 col-form-label">Type</label>
    <div class="col-9">
      <input type="hidden" name="type_code" value="{{ $matter->type_code or '' }}" />
      <input type="text" class="form-control" id="type_code" value="{{ @$matter->type->type or '' }}" onFocus="this.select()" />
    </div>
  </div>
  <div class="form-group row">
    <label for="caseref" class="col-3 col-form-label font-weight-bold">Caseref</label>
    <div class="col-9">
      <input type="text" class="form-control" id="caseref" name="caseref" value="{{ $matter->caseref }}" readonly />
    </div>
  </div>
  <div class="form-group row">
    <label for="responsible" class="col-3 col-form-label font-weight-bold">Responsible</label>
    <div class="col-9">
      <input type="text" class="form-control" id="responsible" name="responsible" value="{{ $matter->responsible or '' }}" required onFocus="this.select()" />
    </div>
  </div>

  <div>
    <button type="button" id="createMatterSubmit" class="btn btn-primary float-right">Create</button>
  </div>
</form> --}}

<form id="natMatterForm">
	<input type="hidden" name="caseref" value="{{ $matter->caseref }}" />
	<input type="hidden" name="category_code" value="{{ $matter->category_code }}" />
	<input type="hidden" name="origin" value="{{ $matter->country_code }}" />
  <input type="hidden" name="type_code" value="{{ $matter->type_code }}" />
  <input type="hidden" name="idx" value="{{ $matter->idx }}" />
	<label>Country</label>
	<label>Entered date</label>
	<div id="national-phases">
@foreach( $matter->countryInfo->natcountries as $iso => $name )
		<div id="nmp-{{ $iso }}">
			<input type="hidden" name="ncountry[{{ $iso }}]" value="{{ $name }}" />
			<span class="national-countries">{{ $name }}</span>
			<input type="text" name="entered_date[{{ $iso }}]" class="entered-date" id="{{ $iso }}" />
			<input type="hidden" name="alt_entered_date[{{ $iso }}]" id="alt-entered-date-{{ $iso }}" />
			<span class="national-matter-remove" id="{{ $iso }}" title="Remove">&minus;</span>
		</div>
@endforeach
	</div>
	<div id="nmp-add">
		<span title="Add">&plus;</span>
		<label for="additional-country"></label>
		<input type="text" id="additional-country" value="" />
	</div>
	<div id="add-matter-actions">
		<input type="button" name="national-matter-cancel" id="national-matter-cancel" value="Cancel" />
		<input type="button" name="national-matter-submit" id="national-matter-submit" value="Submit" />
	</div>
</form>

<script>

  $('input#country').autocomplete({
		minLength: 2,
		source: "/country/autocomplete",
    change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
      }
		},
		select: function(event, ui) {
      $(this).parent().find('[type="hidden"]').val(ui.item.id);
		}
	});

  $('input#type_code').autocomplete({
		minLength: 0,
		source: "/type/autocomplete",
    change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
      }
		},
		select: function(event, ui) {
      $(this).parent().find('[name="type_code"]').val(ui.item.id);
		}
	}).focus(function () {
    $(this).autocomplete("search", "");
  });

  $('input#responsible').autocomplete({
		minLength: 2,
		source: "/user/autocomplete",
		change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
      }
		},
		select: function(event, ui) {
			this.value = ui.item.value;
		}
  });
</script>
