<form id="createMatterForm" class="ui-front">
  <input type="hidden" name="operation" value="{{ $operation }}" />
  <input type="hidden" name="origin_id" value="{{ $matter->id }}" />
  <input type="hidden" name="origin_container_id" value="{{ $matter->container_id or '' }}" />
  <div class="form-group row">
    <label for="category" class="col-3 col-form-label font-weight-bold">Category</label>
    <div class="col-9">
      <input type="hidden" name="category_code" value="{{ $matter->category_code or '' }}" />
      <input type="text" class="form-control" id="category" value="{{ $matter->category->category or '' }}" onFocus="this.select()" />
    </div>
  </div>
  <div class="form-group row">
    <label for="country" class="col-3 col-form-label font-weight-bold">Country</label>
    <div class="col-9">
      <input type="hidden" name="country" value="{{ $matter->country or '' }}" />
      <input type="text" class="form-control" id="country" value="{{ $matter->countryInfo->name or '' }}" onFocus="this.select()" />
    </div>
  </div>
  <div class="form-group row">
    <label for="origin" class="col-3 col-form-label">Origin</label>
    <div class="col-9">
      <input type="hidden" name="origin" value="{{ $matter->origin or '' }}" />
      <input type="text" class="form-control" id="origin" value="{{ @$matter->originInfo->name or '' }}" onFocus="this.select()" />
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
      <input type="text" class="form-control" id="caseref" name="caseref" value="{{ $matter->caseref or '' }}" onFocus="this.select()" />
    </div>
  </div>
  <div class="form-group row">
    <label for="responsible" class="col-3 col-form-label font-weight-bold">Responsible</label>
    <div class="col-9">
      <input type="text" class="form-control" id="responsible" name="responsible" value="{{ $matter->responsible or '' }}" required onFocus="this.select()" />
    </div>
  </div>

  @if ( $operation == 'child' )
  <fieldset class="form-group">
    <legend>Use current {{ $matter->category->category or 'matter' }} as:</legend>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="priority" value="1" checked="checked" id="priority" />
      <label class="form-check-label" for="priority">Priority application</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="priority" value="0" id="parent" />
      <label class="form-check-label" for="parent">Parent application</label>
    </div>
  </fieldset>
  {{-- <fieldset class="form-group">
    <legend>Child {{ $matter->category->category or 'matter' }}:</legend>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="container" value="1" id="container" />
      <label class="form-check-label" for="container">Is independent container</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="container" value="0" checked="checked" id="inherit" />
      <label class="form-check-label" for="inherit">Inherits its information</label>
    </div>
  </fieldset> --}}
  @endif

  <div>
    <button type="button" id="createMatterSubmit" class="btn btn-primary">Create</button>
  </div>
</form>

<script>
	$('input#category').autocomplete({
		minLength: 2,
		source: "/category/autocomplete",
    change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
      }
		},
		select: function(event, ui) {
      $(this).parent().find('[name="category_code"]').val(ui.item.id);
		}
	});

  $('input#country, input#origin').autocomplete({
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

  $("#createMatterSubmit").click( function() {
    var request = $("#createMatterForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $.post('/matter', request)
    .fail(function(errors) {
      $.each(errors.responseJSON.errors, function (key, item) {
        $("#createMatterForm").find('input[name=' + key + ']').attr("placeholder", item).css('background-color', '#f8d7da');
      });
      $("#createMatterForm").after('<div class="alert alert-danger" role="alert">' + errors.responseJSON.message + '</div>');
    })
    .done(function(data) {
      $(location).attr("href", data);
    });
  });
</script>
