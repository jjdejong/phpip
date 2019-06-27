<card data-id={{ $matter->id }}>
  <div class="card-body">
    <form>
      <table class="table table-hover table-sm">
        <tbody>
          <tr data-id={{ $matter->id }}>
            <td>Category</td>
            <td>
              @if($cat_edit == 1)
              <select name="category_code" id="category_code" class="form-control noformat">
                @foreach ( $cats as $cat )
                <option value="{{ $cat->code }}" {{ $matter->category_code == $cat->code ? 'selected' : "" }}>
                  {{ $cat->category }}
                </option>
                @endforeach
              </select>
              @else
              {{ $matter->category->category }}
              @endif
            </td>
            <td>Country</td>
            <td>
              @if($country_edit == 1)
              <input type="text" class="form-control noformat" name="country" list="ajaxDatalist" data-ac="/country/autocomplete" data-actarget="country" value="{{ $matter->countryInfo->name }}">
              @else
              {{ $matter->countryInfo->name }}
              @endif
            </td>
          </tr>
          <tr data-id={{ $matter->id }}>
            <td>Origin</td>
            <td>
              <input type="text" class="form-control noformat" name="origin" list="ajaxDatalist" data-ac="/country/autocomplete" data-actarget="origin" value="{{  empty($matter->originInfo) ? '' : $matter->originInfo->name  }}">
            </td>
            <td>Type</td>
            <td>
              <select name="type_code" id="type_code" class="form-control-plaintext noformat">
                <option value=""></option>
                @foreach ( $types as $type )
                <option value="{{ $type->code }}" {{ $matter->type_code == $type->code ? 'selected' : "" }}>
                  {{ $type->type }}
                </option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr data-id={{ $matter->id }}>
            <td>
              Caseref
            </td>
            <td>
              <input type="text" class="form-control-plaintext noformat" id="caseref" name="caseref" value="{{ $matter->caseref }}">
            </td>
            <td>Responsible</td>
            <td>
              <input type="text" class="form-control-plaintext noformat" name="responsible" list="ajaxDatalist" data-ac="/user/autocomplete" data-actarget="responsible" value="{{ empty($matter->responsible) ? '' : $matter->responsible }}">
            </td>
          </tr>
          <tr data-id={{ $matter->id }}>
            <td>Parent</td>
            <td>
              <div style="vertical-align: baseline;">
                <input type="text" class="form-control-plaintext noformat" name="parent_id" list="ajaxDatalist" data-ac="/matter/autocomplete" data-actarget="parent_id" value="{{ empty($matter->parent) ? '' : $matter->parent->UID }}">
              </div>
            </td>
            <td>Container
            </td>
            <td>
              <input type="text" class="form-control-plaintext noformat" name="container_id" list="ajaxDatalist" data-ac="/matter/autocomplete" data-actarget="container_id" value="{{ empty($matter->container) ? '' : $matter->container->UID }}">
            </td>
          </tr>
          <tr data-id={{ $matter->id }}>
            <td>Index</td>
            <td>
              <input type="text" class="form-control-plaintext noformat" id="idx" name="idx" value="{{ $matter->idx }}">
            </td>
            <td>Term adjust</td>
            <td>
              <input type="text" class="form-control-plaintext noformat" id="term_adjust" name="term_adjust" value="{{ $matter->term_adjust }}">
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <div title="This value is changed automatically by inserting or deleting killer events, such as 'Abandoned', in the Status">
                {{ $matter->dead == 1 ? 'Dead' : 'Alive' }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
    <span class="col-sm-12">
      <button type="button" class="btn btn-danger float-left" id="deleteMatter">Delete Matter</button>
    </span>
  </div>
</card>
