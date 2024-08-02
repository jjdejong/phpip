@php
$classifiers = $matter->classifiers->groupBy('type_name');
@endphp
<table class="table table-sm table-borderless">
  <thead class="table-light">
    <tr>
      <th class="border-top-0">Type/Value</th>
      <th class="border-top-0">URL</th>
      <th class="border-top-0">Link to matter</th>
      <th class="border-top-0">
        @can('readwrite')
        <a href="#addClassifierRow" data-bs-toggle="collapse">
          <span class="float-end" title="Add classifier">&oplus;</span>
        </a>
        @endcan
      </th>
    </tr>
  </thead>
  @foreach ($classifiers as $type => $classifier_group)
  <tbody>
    <tr>
      <th colspan="4">
        {{ $type }}
      </th>
    </tr>
  </tbody>
  <tbody class="sortable">
    @foreach($classifier_group as $classifier)
    <tr class="reveal-hidden" data-resource="/classifier/{{ $classifier->id }}">
      <td class="ps-2"><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}" {{ $type == 'Image' ? 'disabled' : '' }}></td>
      <td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}"></td>
      <td><input type="text" class="form-control noformat" name="lnk_matter_id" data-ac="/matter/autocomplete" value="{{ $classifier->lnk_matter_id ? $classifier->linkedMatter->uid : '' }}"></td>
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
      <td colspan="4">
        <form id="addClassifierForm">
          <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
          <div class="row">
            <div class="col p-1">
              <input type="hidden" name="type_code" value="">
              <input type="text" class="form-control form-control-sm" size="16" placeholder="Type" data-ac="/classifier-type/autocomplete/0" data-actarget="type_code" data-aclength="0">
            </div>
            <div class="col p-1 hideForFile">
              <input type="text" class="form-control form-control-sm px-1" name="value" placeholder="Value">
            </div>
            <div class="col p-1 hideForFile">
              <input type="url" class="form-control form-control-sm px-1" name="url" placeholder="URL">
            </div>
            <div class="col p-1 hideForFile">
              <input type="hidden" name="lnk_matter_id" value="">
              <input type="text" class="form-control form-control-sm px-1" placeholder="Linked to" data-ac="/matter/autocomplete" data-actarget="lnk_matter_id">
            </div>
            <div class="col-7 p-1 d-none" id="forFile">
              <input type="file" class="form-control form-control-sm" name="image">
            </div>
            <div class="col-2 p-1 btn-group btn-group-sm">
              <button type="button" class="btn btn-primary" id="addClassifierSubmit">&check;</button>
              <button type="reset" class="btn btn-outline-primary" id="addClassifierReset">&times;</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
  </tbody>
</table>
