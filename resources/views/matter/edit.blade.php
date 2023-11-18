<div class="alert alert-info">{{ _i("Some fields cannot be changed once the matter is filed, because this would affect the deadline calculations") }}</div>
<form data-resource="/matter/{{ $matter->id }}">
  <table class="table">
    <tbody>
      <tr>
        <th width="25%">
          Caseref
        </th>
        <td>
          <input type="text" class="form-control noformat" name="caseref" value="{{ $matter->caseref }}">
        </td>
      </tr>
      <tr>
        <th>
          @if($cat_edit == 1)
          <input type="text" class="form-control noformat" name="category_code" data-ac="/category/autocomplete" data-aclength="0" placeholder="{{ $matter->category->category }}">
          @else
          <span title="{{ _i('Cannot be changed because the matter has tasks based on the category') }}">
            {{ $matter->category->category }}
          </span>
          @endif
        </th>
        <td>
          <div title="This value is changed automatically by inserting or deleting killer events, such as 'Abandoned', in the Status">
            {{ $matter->dead == 1 ? 'Inactive' : 'Active' }}
          </div>
        </td>
      </tr>
      <tr>
        <th>{{ _i("Country") }}</th>
        <td>
          @if($country_edit == 1)
          <input type="text" class="form-control noformat text-truncate" name="country" data-ac="/country/autocomplete" placeholder="{{ _i($matter->countryInfo->name) }}">
          @else
          <span title="{{ _i('Cannot be changed because the matter has tasks based on the country') }}">
            {{ $matter->countryInfo->name }}
          </span>
          @endif
        </td>
      </tr>
      <tr>
        <th>{{ _i("Origin") }}</th>
        <td>
          <input type="text" class="form-control noformat text-truncate" name="origin" data-ac="/country/autocomplete" value="{{ empty($matter->originInfo) ? '' : $matter->originInfo->name }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Type") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="type_code" data-ac="/type/autocomplete" data-aclength="0" value="{{ $matter->type->type ?? '' }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Responsible") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="responsible" data-ac="/user/autocomplete" placeholder="{{ $matter->responsible }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Parent") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="parent_id" data-ac="/matter/autocomplete" value="{{ empty($matter->parent) ? '' : $matter->parent->uid }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Container") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="container_id" data-ac="/matter/autocomplete" value="{{ empty($matter->container) ? '' : $matter->container->uid }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Index") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="idx" value="{{ $matter->idx }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Alt. ref") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="alt_ref" value="{{ $matter->alt_ref }}">
        </td>
      </tr>
      <tr>
        <th>{{ _i("Term adjust (days)") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="term_adjust" value="{{ $matter->term_adjust }}">
        </td>
      </tr>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger float-start" id="deleteMatter">Delete Matter</button>
</form>
