@php
  // Get the collection from the matter
  $classifiersCollection = $matter->classifiers ?? collect(); // Use loaded relationship or fallback

  // Group by type_code
  $groupedClassifiersByCode = $classifiersCollection->groupBy('type_code');

  // Sort groups by the display_order of the related ClassifierType
  $sortedClassifierGroups = $groupedClassifiersByCode->sortBy(function ($group, $type_code) {
      // Access the classifierType relationship on the first item of the group
      // Ensure classifierType relationship is eager-loaded or available on the MatterClassifiers model/view
      return $group->first()?->classifierType?->display_order ?? 127;
  });
@endphp
<table class="table table-sm table-borderless">
  <thead class="table-light">
    <tr>
      <th class="border-top-0">{{ __('Type/Value') }}</th>
      <th class="border-top-0">{{ __('URL') }}</th>
      <th class="border-top-0">{{ __('Link to matter') }}</th>
      <th class="border-top-0">
        @can('readwrite')
        <a href="#addClassifierRow" data-bs-toggle="collapse">
          <span class="float-end" title="{{ __('Add classifier') }}">
            <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#plus-circle-fill"/></svg>
          </span>
        </a>
        @endcan
      </th>
    </tr>
  </thead>
  {{-- Loop through the sorted groups --}}
  @foreach ($sortedClassifierGroups as $type_code => $classifier_group)
    @php
      // Get the related ClassifierType model instance from the first item
      $classifierTypeModel = $classifier_group->first()?->classifierType;
      // Get the TRANSLATED type name
      $translatedTypeName = $classifierTypeModel?->type ?? $type_code; // Fallback to code
      $isImageType = $translatedTypeName == 'Image'; // Check if it's the Image type
    @endphp
  <tbody>
    <tr>
      <th colspan="4">
        {{-- Display the translated type name --}}
        {{ $translatedTypeName }}
      </th>
    </tr>
  </tbody>
  <tbody class="sortable">
    {{-- Loop through classifiers within this group --}}
    @foreach($classifier_group as $classifier)
    <tr class="reveal-hidden" data-resource="/classifier/{{ $classifier->id }}">
      {{-- Use $isImageType to disable input --}}
      <td class="ps-2"><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}" {{ $isImageType ? 'disabled' : '' }}></td>
      <td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}"></td>
      <td><input type="text" class="form-control noformat" name="lnk_matter_id" data-ac="/matter/autocomplete" value="{{ $classifier->lnk_matter_id ? $classifier->linkedMatter->uid : '' }}"></td>
      <td>
        <input type="hidden" name="display_order" value="{{ $classifier->display_order }}" />
        <a href="#" class="hidden-action text-danger" id="deleteClassifier" title="{{ __('Delete classifier') }}">
          <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#trash-fill"></use></svg>
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
  @endforeach
  <tbody>
    <tr id="addClassifierRow" class="collapse">
      <td colspan="4">
        <form id="addClassifierForm" x-data="{ isImageType: false }">
          <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
          <div class="row">
            <div class="col p-1">
              <input type="hidden" name="type_code" value="">
              <input type="text" class="form-control form-control-sm" size="16" placeholder="{{ __('Type') }}"
                     data-ac="/classifier-type/autocomplete/0" data-actarget="type_code" data-aclength="0"
                     @ac-completed="isImageType = ($event.detail.value === 'Image')">
            </div>
            <div class="col p-1" x-show="!isImageType">
              <input type="text" class="form-control form-control-sm px-1" name="value" placeholder="{{ __('Value') }}">
            </div>
            <div class="col p-1" x-show="!isImageType">
              <input type="url" class="form-control form-control-sm px-1" name="url" placeholder="{{ __('URL') }}">
            </div>
            <div class="col p-1" x-show="!isImageType">
              <input type="hidden" name="lnk_matter_id" value="">
              <input type="text" class="form-control form-control-sm px-1" placeholder="{{ __('Linked to') }}" data-ac="/matter/autocomplete" data-actarget="lnk_matter_id">
            </div>
            <div class="col-7 p-1" x-show="isImageType" id="forFile">
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
