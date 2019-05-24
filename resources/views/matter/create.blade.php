<form id="createMatterForm" autocomplete="off" class="ui-front">
  <input type="hidden" name="operation" value="{{ $operation ?? "new" }}" />
  <input type="hidden" name="origin_id" value="{{ $from_matter->id ?? '' }}" />
  <div class="form-group row">
    <label for="category" class="col-3 col-form-label font-weight-bold">Category</label>
    <div class="col-9">
      <input type="hidden" name="category_code" value="{{ $from_matter->category_code ?? ( $category['code'] ?? '') }}" />
      <input id="category_code" type="text" class="form-control" data-ac="/category/autocomplete" data-actarget="category_code" value="{{ $category['name'] ?? ( $from_matter->category->category ??  '' ) }}" autocomplete="off">
    </div>
  </div>
  <div class="form-group row">
    <label for="country" class="col-3 col-form-label font-weight-bold">Country</label>
    <div class="col-9">
      <input type="hidden" name="country" value="{{ $from_matter->country ?? '' }}" />
      <input id="country" type="text" class="form-control" data-ac="/country/autocomplete" data-actarget="country" value="{{ $from_matter->countryInfo->name ?? '' }}" autocomplete="off">
    </div>
  </div>
  <div class="form-group row">
    <label for="origin" class="col-3 col-form-label">Origin</label>
    <div class="col-9">
      <input type="hidden" name="origin" value="{{ $from_matter->origin ?? '' }}" />
      <input id="origin" type="text" class="form-control" data-ac="/country/autocomplete" data-actarget="origin" value="{{ $from_matter->originInfo->name ?? '' }}" autocomplete="off">
    </div>
  </div>
  <div class="form-group row">
    <label for="type_code" class="col-3 col-form-label">Type</label>
    <div class="col-9">
      <input type="hidden" name="type_code" value="{{ $from_matter->type_code ?? '' }}" />
      <input id="type_code" type="text" class="form-control" data-ac="/type/autocomplete" data-actarget="type_code" value="{{ $from_matter->type->type ?? '' }}" autocomplete="off">
    </div>
  </div>
  <div class="form-group row">
    <label for="caseref" class="col-3 col-form-label font-weight-bold">Caseref</label>
    <div class="col-9">
      @if ( $operation == 'child' )
      <input type="text" class="form-control" id="caseref" name="caseref" value="{{ $from_matter->caseref ?? '' }}" readonly />
      @else
      <input type="text" class="form-control" id="caseref" data-ac="/matter/new-caseref" name="caseref" value="{{ $from_matter->caseref ?? ( $category['next_caseref'] ?? '') }}" autocomplete="off">
      @endif
    </div>
  </div>
  <div class="form-group row">
    <label for="responsible" class="col-3 col-form-label font-weight-bold">Responsible</label>
    <div class="col-9">
      <input type="text" class="form-control" id="responsible" data-ac="/user/autocomplete" name="responsible" value="{{ $from_matter->responsible ?? Auth::user()->login }}" autocomplete="off">
    </div>
  </div>

  @if ( $operation == 'child' )
  <fieldset class="form-group">
    <legend>Use current {{ $from_matter->category->category ?? 'matter' }} as:</legend>
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
    <legend>Child {{ $from_matter->category->category ?? 'matter' }}:</legend>
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
    <button type="submit" id="createMatterSubmit" class="btn btn-primary">Create</button>
  </div>
</form>

<script>
  var createMatterForm = document.forms['createMatterForm'];
  createMatterForm.addEventListener('click', (e) => {
    if (e.target.hasAttribute('data-ac')) {
      autocompleteJJ(e.target, e.target.dataset.ac, e.target.dataset.actarget);
    }
  });

  var autocompleteJJ = (searchField, dataSource, targetField) => {
    /* "searchField" is the element receiving the user input,
     * "dataSource" is the Ajax resource URL, and
     * "targetField" is an (optional) input field name receiving the "id" value
     * The Ajax resource returns a list of JSON id/value pairs, sometimes a label
     * */

    // Start by removing stray result lists that can remain when clicking erratically
    if (tmp = document.getElementById('match-list')) tmp.remove();
    // Create a fresh result list attached to the current element
    searchField.insertAdjacentHTML('afterend', '<div id="match-list" class="dropdown-menu bg-light"></div>');
    var matchList = document.getElementById('match-list');
    var targetElement = "",
      items = "",
      selectedItem = "";
    if (targetField) {
      targetElement = document.getElementsByName(targetField)[0];
    }
    // Get items
    var getItems = async (term) => {
      if (term.length > 0) {
        let res = await fetch(dataSource + '?term=' + term);
        items = await res.json();
        if (items.length === 0) {
          $('#match-list').dropdown('hide');
        } else {
          $('#match-list').dropdown('show');
          if (items.length > 0) {
            let html = items.map(
              match => `<button class="dropdown-item py-1" type="button" id="${match.id ? match.id : match.value}" data-value="${match.value}">${match.label ? match.label : match.value}</button>`
            ).join('');
            matchList.innerHTML = html;
          };
        }
      } else {
        $('#match-list').dropdown('hide');
      }
    };

    searchField.addEventListener('input', () => getItems(searchField.value));
    matchList.onclick = (e) => {
      // Retrieve complete selected item, in case it contains more than id, value or label
      selectedItem = items.filter((item) => {
        return item.value === e.target.dataset.value;
      });
      console.log(selectedItem);
      searchField.value = e.target.dataset.value;
      if (targetField) {
        targetElement.value = e.target.id;
      }
      matchList.remove();
    };
  }

  createMatterForm.addEventListener('submit', function(ev) {
    ev.preventDefault();

    formData = new FormData(createMatterForm);
    searchParams = new URLSearchParams(formData);
    /*searchParams.forEach( (value, key) => {
      if (value.length === 0) searchParams.delete(key);
    });*/

    fetch('/matter', {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        method: 'POST',
        body: searchParams
      })
      .then(response => {
        if (!response.ok) {
          return response.json();
        } else {
          return response.text();
        }
      })
      .then(data => {
        if (data.errors) {
          // Form validation error notification
          Object.entries(data.errors).forEach(([key, value]) => {
            inputElt = createMatterForm.elements[key];
            // "key" matches with both the element's name and id, so inputElt can contain two input elements (the hidden one with id=key and the visible one with name=key)
            if (inputElt.length) {
              // If defined, inputElt contains two elements and we need the visible one (the second one)
              inputElt = inputElt[1];
            }
            inputElt.placeholder = value;
            inputElt.classList.add('is-invalid');
          });
          newMatterModal.querySelector('.modal-footer').insertAdjacentHTML('afterbegin', `<div class="alert alert-danger" role="alert">${data.message}</div>`);
        } else {
          // Redirect to the created matter (link returned by MatterController.store())
          location.href = data;
        }
      })
      .catch(error => {
        console.log(error);
      });

  }, false);
</script>
