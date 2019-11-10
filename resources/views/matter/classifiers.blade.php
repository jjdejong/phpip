@php
$classifiers = $matter->classifiers->groupBy('type_name');
@endphp
<table class="table table-sm">
  <thead class="thead-light">
    <tr>
      <th class="border-top-0">Type/Value</th>
      <th class="border-top-0">URL</th>
      <th class="border-top-0">Link to matter</th>
      <th class="border-top-0">
        <a href="#addClassifierRow" data-toggle="collapse">
          <span class="float-right" title="Add classifier">&oplus;</span>
        </a>
      </th>
    </tr>
  </thead>
  @foreach ($classifiers as $type => $classifier_group)
  <tbody>
    <tr>
      <td colspan="4" class="font-weight-bold">
        {{ $type }}
      </td>
    </tr>
  </tbody>
  <tbody class="sortable">
    @foreach($classifier_group as $classifier)
    <tr class="reveal-hidden" data-resource="/classifier/{{ $classifier->id }}">
      <td><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}"></td>
      <td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}"></td>
      <td><input type="text" class="form-control noformat" name="lnk_matter_id" list="ajaxDatalist" data-ac="/matter/autocomplete" value="{{ $classifier->lnk_matter_id ? $classifier->linkedMatter->uid : '' }}"></td>
      <td>
        <input type="hidden" name="display_order" value="{{ $classifier->display_order }}" />
        <a href="#" class="hidden-action text-danger" id="deleteClassifier" title="Delete classifier">
          &CircleMinus;
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
  @endforeach
  <tbody>
    <tr id="addClassifierRow" class="collapse">
      <td colspan="5">
        <form id="addClassifierForm" class="form-inline">
          <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
          <div class="input-group">
            <input type="hidden" name="type_code" value="">
            <input type="text" class="form-control form-control-sm" size="16" placeholder="Type" list="ajaxDatalist" data-ac="/classifier-type/autocomplete/0" data-actarget="type_code">
            <input type="text" class="form-control form-control-sm" size="10" name="value" placeholder="Value">
            <input type="url" class="form-control form-control-sm" size="16" name="url" placeholder="URL">
            <input type="hidden" name="lnk_matter_id" value="">
            <input type="text" class="form-control form-control-sm" size="16" placeholder="Linked to" list="ajaxDatalist" data-ac="/matter/autocomplete" data-actarget="lnk_matter_id">
            <div class="input-group-append">
              <button type="button" class="btn btn-primary btn-sm" id="addClassifierSubmit">&check;</button>
              <button type="reset" class="btn btn-outline-primary btn-sm" onClick="$('#addClassifierRow').collapse('hide')">&times;</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
  </tbody>
</table>
