<form id="natMatterForm" class="ui-front">
	<input type="hidden" name="caseref" value="{{ $from_matter->caseref }}" />
	<input type="hidden" name="category_code" value="{{ $from_matter->category_code }}" />
	<input type="hidden" name="origin" value="{{ $from_matter->country }}" />
  <input type="hidden" name="type_code" value="{{ $from_matter->type_code }}" />
  <input type="hidden" name="idx" value="{{ $from_matter->idx }}" />
	<input type="hidden" name="origin_id" value="{{ $from_matter->id }}" />
  <input type="hidden" name="origin_container_id" value="{{ $from_matter->container_id or '' }}" />
  <input type="hidden" name="responsible" value="{{ $from_matter->responsible }}" />
	<div id="ncountries">
    @foreach( $from_matter->countryInfo->natcountries as $iso => $name )
      <div class="input-group" id="country-{{ $iso }}">
        <input type="hidden" name="ncountry[]" value="{{ $iso }}" />
    		<input type="text" class="form-control" readonly value="{{ $name }}" />
        <div class="input-group-append">
          <button class="btn btn-outline-danger" type="button" id="{{ $iso }}" title="Remove {{ $iso }}">&CircleMinus;</button>
        </div>
      </div>
    @endforeach
	</div>
	<div class="input-group">
    <input type="text" class="form-control" placeholder="Add country" id="country" />
    <div class="input-group-append">
      <span class="input-group-text">&oplus;</span>
    </div>
	</div>
	<div id="add-matter-actions">
		<button type="button" class="btn btn-primary" id="nationalizeSubmit">Submit</button>
	</div>
</form>

<script>

  $('input#country').autocomplete({
		minLength: 2,
		source: "/country/autocomplete",
		select: function(event, ui) {
			var new_country = '<div class="input-group" id="country-' + ui.item.id + '"> \
        <input type="hidden" name="ncountry[]" value="' + ui.item.id + '" /> \
    		<input type="text" class="form-control" readonly value="' + ui.item.value + '" /> \
        <div class="input-group-append"> \
          <button class="btn btn-outline-danger" type="button" id="' + ui.item.id + '" title="Remove ' + ui.item.id + '">&CircleMinus;</button> \
        </div> \
      </div>';
			$('#ncountries').append(new_country);
		},
		close: function (event, ui) {
      $(this).val("");
		}
	});

	$("#nationalizeSubmit").click( function() {
    var request = $("#natMatterForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $.post('/matter/storeN', request)
    .fail(function(errors) {
      $("#natMatterForm").after('<div class="alert alert-danger" role="alert">' + errors.responseJSON.message + '</div>');
			$.each(errors.responseJSON.errors, function (key, item) {
        $(".alert-danger").append("<br>- " + item);
      });
    })
    .done(function(data) {
      $(location).attr("href", data);
    });
  });

	$("#ncountries").on("click", ".btn-outline-danger", function(){
		$('#country-' + $(this).attr('id')).remove();
	});

</script>
