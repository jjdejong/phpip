<form id="createMatterForm" autocomplete="off">
  <input type="hidden" name="operation" value="{{ $operation ?? "new" }}">
  <div class="row">
    <label for="category" class="col-4 col-form-label fw-bold">Category</label>
    <div class="col-8">
      <input type="hidden" name="category_code" value="{{ $parent_matter->category_code ?? ( $category['code'] ?? '') }}">
      <input type="text" class="form-control" data-ac="/category/autocomplete" data-actarget="category_code" data-aclength="0" placeholder="{{ $category['name'] ?? ( $parent_matter->category->category ??  '' ) }}" autocomplete="off">
    </div>
  </div>
  @if ( $operation == 'ops' )
  <div class="row">
    <label for="docnum" class="col-4 col-form-label fw-bold">Pub Number</label>
    <div class="col-8">
      <input type="text" name="docnum" class="form-control" placeholder="CCNNNNNN">
    </div>
    <small class="form-text text-muted">
      Publication number prefixed with the country code and optionally suffixed with the kind code. 
      No spaces nor non-alphanumeric characters. 
      {{-- Application number in DOCDB format: country code followed by the number (only digits, no spaces and without the ending ".n"). 
      For numbers without a two-digit year (like the US), insert YY. For PCTs: CCYYYY012345W. --}}
    </small>
  </div>
  <div class="row">
    <label for="client_id" class="col-4 col-form-label fw-bold">Client</label>
    <div class="col-8">
      <input type="hidden" name="client_id">
      <input type="text" class="form-control" data-ac="/actor/autocomplete" data-actarget="client_id" autocomplete="off">
    </div>
  </div>
  @else
  <input type="hidden" name="parent_id" value="{{ $parent_matter->id ?? '' }}">
  <div class="row">
    <label for="country" class="col-4 col-form-label fw-bold">Country</label>
    <div class="col-8">
      <input type="hidden" name="country" value="{{ $parent_matter->country ?? '' }}">
      <input type="text" class="form-control text-truncate" data-ac="/country/autocomplete" data-actarget="country" placeholder="{{ $parent_matter->countryInfo->name ?? '' }}" autocomplete="off">
    </div>
  </div>
  <div class="row">
    <label for="origin" class="col-4 col-form-label">Origin</label>
    <div class="col-8">
      <input type="hidden" name="origin" value="{{ $parent_matter->origin ?? '' }}">
      <input type="text" class="form-control text-truncate" data-ac="/country/autocomplete" data-actarget="origin" placeholder="{{ $parent_matter->originInfo->name ?? '' }}" autocomplete="off">
    </div>
  </div>
  <div class="row">
    <label for="type_code" class="col-4 col-form-label">Type</label>
    <div class="col-8">
      <input type="hidden" name="type_code" value="{{ $parent_matter->type_code ?? '' }}">
      <input type="text" class="form-control" data-ac="/type/autocomplete" data-actarget="type_code" data-aclength="0" value="{{ $parent_matter->type->type ?? '' }}" autocomplete="off">
    </div>
  </div>
  @endif
  <div class="row">
    <label for="caseref" class="col-4 col-form-label fw-bold">Caseref</label>
    <div class="col-8">
      @if ( $operation == 'child' )
      <input type="text" class="form-control" name="caseref" value="{{ $parent_matter->caseref ?? '' }}" readonly>
      @else
      <input type="text" class="form-control" data-ac="/matter/new-caseref" name="caseref" value="{{ $parent_matter->caseref ?? ( $category['next_caseref'] ?? '') }}" autocomplete="off">
      @endif
    </div>
  </div>
  <div class="row">
    <label for="responsible" class="col-4 col-form-label fw-bold">Responsible</label>
    <div class="col-8">
      <input type="hidden" name="responsible" value="{{ $parent_matter->responsible ?? Auth::user()->login }}">
      <input type="text" class="form-control" data-ac="/user/autocomplete" data-actarget="responsible" placeholder="{{ $parent_matter->responsible ?? Auth::user()->name }}" autocomplete="off">
    </div>
  </div>

  @if ( $operation == 'child' )
  <fieldset class="form-group">
    <legend>Use current {{ $parent_matter->category->category ?? 'matter' }} as:</legend>
    <div class="form-check my-1">
      <input class="form-check-input mt-0" type="radio" name="priority" value="1" checked>
      <label class="form-check-label">Priority application</label>
    </div>
    <div class="form-check my-1">
      <input class="form-check-input mt-0" type="radio" name="priority" value="0">
      <label class="form-check-label">Parent application</label>
    </div>
  </fieldset>
  @endif

  <div>
    @if ( $operation == 'ops' )
    <button type="button" id="createFamilySubmit" class="btn btn-primary">Create</button>
    @else
    <button type="button" id="createMatterSubmit" class="btn btn-primary">Create</button>
    @endif
  </div>
</form>
