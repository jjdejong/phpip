
<card data-id={{ $matter->id }} >
    <div class="card-body">
        <form>
            <div class="form-group row">
                <label for="category_code" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-4">
                    @if($cat_edit == 1)
                        <select name="category_code" id="category_code"  class="form-control form-control-sm edit">
                        @foreach ( $cats as $cat )
                            <option value="{{ $cat->code }}"
                                {{ $matter->category_code == $cat->code ? "selected=selected" : "" }}>
                                {{ $cat->category }}
                            </option>
                        @endforeach
                        </select>
                    @else
                        <div class="col-form-label">
                            {{ $matter->category->category }}
                        </div>
                    @endif
                </div>
                <label for="country" class="col-sm-2 col-form-label">Country</label>
                @if($country_edit == 1)
                    <div class="col-sm-4 ui-front">
                        <input type="text" class="form-control form-control-sm" id="country" name="country" value="{{ $matter->countryInfo->name }}">
                    </div>
                @else
                    <div class="col-form-label">
                        {{ $matter->countryInfo->name }}
                    </div>
                @endif
            </div>
            <div class="form-group row">
                <label for="origin" class="col-sm-2 col-form-label">Origin</label>
                <div class="col-sm-4 ui-front">
                    <input type="text" class="form-control form-control-sm edit" id="origin" name="origin"
                        value="{{  empty($matter->originInfo) ? '' : $matter->originInfo->name  }}">
                </div>
                <label for="type_code" class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-4">
                    <select name="type_code" id="type_code" class="form-control form-control-sm edit" >
                        <option value=""></option>
                    @foreach ( $types as $type )
                        <option value="{{ $type->code }}"
                            {{ $matter->type_code == $type->code ? "selected=selected" : "" }}>
                            {{ $type->type }}
                        </option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="caseref" class="col-sm-2 col-form-label">Caseref</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control-sm edit" id="caseref" name="caseref"
                        value="{{ $matter->caseref }}">
                </div>
                <label for="responsible" class="col-sm-2 col-form-label">Responsible</label>
                <div class="col-sm-4 ui-front">
                    <input type="text" class="form-control form-control-sm edit" id="responsible" name="responsible"
                        value="{{ empty($matter->responsible) ? '' : $matter->responsible }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="parent_id" class="col-sm-2 col-form-label">Parent</label>
                <div class="col-sm-4 ui-front">
                    <input type="text" class="form-control form-control-sm edit" id="parent_id" name="parent_id"
                        value="{{ empty($matter->parent) ? '' : $matter->parent->UID }}">
                </div>
                <label for="container_id" class="col-sm-2 col-form-label">Container</label>
                <div class="col-sm-4 ui-front">
                    <input type="text" class="form-control form-control-sm edit" id="container_id" name="container_id"
                        value="{{ empty($matter->container) ? '' : $matter->container->UID }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="idx" class="col-sm-2 col-form-label">Index</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control-sm edit" id="idx" name="idx"
                        value="{{ $matter->idx }}">
                </div>
                <label for="dead" class="col-sm-2 col-form-label">Dead</label>
                <div class="col-sm-4"  title="This value is changed automatically by inserting or deleting killer events, such as 'Abandoned', in the Status">
                    <div class="col-form-label">{{ $matter->dead == 1 ? 'Yes' : 'No' }}</div>
                </div>
            </div>
            <div class="form-group row">
                <label for="term_adjust" class="col-sm-2 col-form-label">Term adjust</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control-sm edit" id="term_adjust" name="term_adjust"
                        value="{{ $matter->term_adjust }}">
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-danger float-right" id="deleteMatter">Delete Matter</button>
                </div>
            </div>
        </form>
    </div>
</card>
