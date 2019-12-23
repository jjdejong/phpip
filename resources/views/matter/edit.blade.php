<form data-resource="/matter/{{ $matter->id }}">
  <table class="table table-hover table-sm">
    <tbody>
      <tr>
        <th>
          @if($cat_edit == 1)
          <select name="category_code" class="custom-select noformat">
            @foreach ( $cats as $cat )
            <option value="{{ $cat->code }}" {{ $matter->category_code == $cat->code ? 'selected' : "" }}>
              {{ $cat->category }}
            </option>
            @endforeach
          </select>
          @else
          <span title="Cannot be changed because the matter has children">
            {{ $matter->category->category }}
          </span>
          @endif
        </th>
        <td>
          <div title="This value is changed automatically by inserting or deleting killer events, such as 'Abandoned', in the Status">
            {{ $matter->dead == 1 ? 'Dead' : 'Alive' }}
          </div>
        </td>
        <td>Country</td>
        <td>
          @if($country_edit == 1)
          <input type="text" class="form-control noformat" name="country" data-ac="/country/autocomplete" placeholder="{{ $matter->countryInfo->name }}">
          @else
          <span title="Cannot be changed because the matter has children">
            {{ $matter->countryInfo->name }}
          </span>
          @endif
        </td>
      </tr>
      <tr>
        <td>Origin</td>
        <td>
          <input type="text" class="form-control noformat" name="origin" data-ac="/country/autocomplete" value="{{ empty($matter->originInfo) ? '' : $matter->originInfo->name }}">
        </td>
        <td>Type</td>
        <td>
          <select name="type_code" class="custom-select noformat">
            <option value=""></option>
            @foreach ( $types as $type )
            <option value="{{ $type->code }}" {{ $matter->type_code == $type->code ? 'selected' : "" }}>
              {{ $type->type }}
            </option>
            @endforeach
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Caseref
        </td>
        <td>
          <input type="text" class="form-control noformat" id="caseref" name="caseref" value="{{ $matter->caseref }}">
        </td>
        <td>Responsible</td>
        <td>
          <input type="text" class="form-control noformat" name="responsible" data-ac="/user/autocomplete" data-actarget="responsible" value="{{ empty($matter->responsible) ? '' : $matter->responsible }}">
        </td>
      </tr>
      <tr>
        <td>Parent</td>
        <td>
          <div style="vertical-align: baseline;">
            <input type="text" class="form-control noformat" name="parent_id" data-ac="/matter/autocomplete" data-actarget="parent_id" value="{{ empty($matter->parent) ? '' : $matter->parent->UID }}">
          </div>
        </td>
        <td>Container
        </td>
        <td>
          <input type="text" class="form-control noformat" name="container_id" data-ac="/matter/autocomplete" data-actarget="container_id" value="{{ empty($matter->container) ? '' : $matter->container->UID }}">
        </td>
      </tr>
      <tr>
        <td>Index</td>
        <td>
          <input type="text" class="form-control noformat" id="idx" name="idx" value="{{ $matter->idx }}">
        </td>
        <td>Term adjust</td>
        <td>
          <input type="text" class="form-control noformat" id="term_adjust" name="term_adjust" value="{{ $matter->term_adjust }}">
        </td>
      </tr>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger float-left" id="deleteMatter">Delete Matter</button>
</form>
