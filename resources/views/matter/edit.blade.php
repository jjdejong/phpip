<div class="alert alert-info">{{ __("Some fields cannot be changed once the matter is filed, because this would affect the deadline calculations") }}</div>
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
          @if($cat_edit)
          <input type="text" class="form-control noformat" name="category_code" data-ac="/category/autocomplete" data-aclength="0" placeholder="{{ $matter->category->category }}">
          @else
          <span title="{{ __('Cannot be changed because the matter has tasks based on the category') }}">
            {{ $matter->category->category }}
          </span>
          @endif
        </th>
        <td>
          <div title="{{ __('This value is changed automatically by inserting or deleting killer events, such as \'Abandoned\', in the Status') }}">
            {{ $matter->dead == 1 ? 'Inactive' : 'Active' }}
          </div>
        </td>
      </tr>
      <tr>
        <th>{{ __("Country") }}</th>
        <td>
          @if($country_edit)
          <input type="text" class="form-control noformat text-truncate" name="country" data-ac="/country/autocomplete" placeholder="{{ __($matter->countryInfo->name) }}">
          @else
          <span title="{{ __('Cannot be changed because the matter has tasks based on the country') }}">
            {{ $matter->countryInfo->name }}
          </span>
          @endif
        </td>
      </tr>
      <tr>
        <th>{{ __("Origin") }}</th>
        <td>
          <input type="text" class="form-control noformat text-truncate" name="origin" data-ac="/country/autocomplete" value="{{ empty($matter->originInfo) ? '' : $matter->originInfo->name }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Type") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="type_code" data-ac="/type/autocomplete" data-aclength="0" value="{{ $matter->type->type ?? '' }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Responsible") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="responsible" data-ac="/user/autocomplete" placeholder="{{ $matter->responsible }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Parent") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="parent_id" data-ac="/matter/autocomplete" value="{{ empty($matter->parent) ? '' : $matter->parent->uid }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Container") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="container_id" data-ac="/matter/autocomplete" value="{{ empty($matter->container) ? '' : $matter->container->uid }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Index") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="idx" value="{{ $matter->idx }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Alt. ref") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="alt_ref" value="{{ $matter->alt_ref }}">
        </td>
      </tr>
      <tr>
        <th>{{ __("Term adjust (days)") }}</th>
        <td>
          <input type="text" class="form-control noformat" name="term_adjust" value="{{ $matter->term_adjust }}">
        </td>
      </tr>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger float-start" id="deleteMatter">{{ __('Delete Matter') }}</button>
</form>
