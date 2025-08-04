@can('admin')
<div data-resource="/countries/{{ $country->iso }}" class="reload-part">
  <table class="table table-hover table-sm">
    <tr>
      <th width="20%">{{ __('ISO Code') }}</th>
      <td>
        @if($country->numcode)
          <span class="form-control-plaintext">{{ $country->iso }}</span>
        @else
          <input type="text" class="form-control" value="{{ $country->iso }}" readonly>
        @endif
      </td>
    </tr>
    
    <tr>
      <th>{{ __('Name') }}</th>
      <td>
        <div class="input-group input-group-sm">
          @if($country->numcode)
            {{-- Standard countries - show as read-only --}}
            <span class="form-control form-control-sm" style="background-color: #f8f9fa; border: 1px solid #ced4da; padding: 0.25rem 0.5rem;">
              @php
                $currentLocale = app()->getLocale();
                $translatedName = $country->getTranslation('name', $currentLocale, false);
                echo $translatedName ?: $country->getTranslation('name', 'en', false) ?: 'Name not available';
              @endphp
            </span>
            <select class="form-select form-select-sm" style="max-width: 80px;" id="nameLocaleSelectReadonly">
              <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
              <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>FR</option>
              <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>DE</option>
            </select>
          @else
            {{-- Custom countries - editable --}}
            <input type="text" class="form-control form-control-sm noformat" name="name[{{ app()->getLocale() }}]" value="{{ $country->getTranslation('name', app()->getLocale(), false) ?: $country->getTranslation('name', 'en', false) ?: '' }}">
            <select class="form-select form-select-sm" style="max-width: 80px;" id="nameLocaleSelect">
              <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
              <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>FR</option>
              <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>DE</option>
            </select>
          @endif
        </div>
        @if(!$country->numcode)
          <div id="nameInputs" style="display: none;">
            <input type="text" class="form-control form-control-sm noformat mt-2" name="name[en]" value="{{ $country->getTranslation('name', 'en', false) ?: '' }}" data-locale="en" {{ app()->getLocale() == 'en' ? 'style=display:none' : '' }}>
            <input type="text" class="form-control form-control-sm noformat mt-2" name="name[fr]" value="{{ $country->getTranslation('name', 'fr', false) ?: '' }}" data-locale="fr" {{ app()->getLocale() == 'fr' ? 'style=display:none' : '' }}>
            <input type="text" class="form-control form-control-sm noformat mt-2" name="name[de]" value="{{ $country->getTranslation('name', 'de', false) ?: '' }}" data-locale="de" {{ app()->getLocale() == 'de' ? 'style=display:none' : '' }}>
          </div>
        @endif
      </td>
    </tr>
    <tr>
      <th>{{ __('Base renewal') }}</th>
      <td>
        <select class="form-select form-select-sm noformat" name="renewal_base">
          <option value="">{{ __('None') }}</option>
          <option value="FIL" {{ $country->renewal_base == 'FIL' ? 'selected' : '' }}>{{ __('Filed') }}</option>
          <option value="GRT" {{ $country->renewal_base == 'GRT' ? 'selected' : '' }}>{{ __('Granted') }}</option>
          <option value="PUB" {{ $country->renewal_base == 'PUB' ? 'selected' : '' }}>{{ __('Published') }}</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>{{ __('First renewal') }}</th>
      <td>
        <input type="number" class="form-control form-control-sm noformat" name="renewal_first" value="{{ $country->renewal_first }}">
      </td>
    </tr>
    <tr>
      <th>{{ __('Start from') }}</th>
      <td>
        <select class="form-select form-select-sm noformat" name="renewal_start">
          <option value="">{{ __('None') }}</option>
          <option value="FIL" {{ $country->renewal_start == 'FIL' ? 'selected' : '' }}>{{ __('Filed') }}</option>
          <option value="GRT" {{ $country->renewal_start == 'GRT' ? 'selected' : '' }}>{{ __('Granted') }}</option>
          <option value="PUB" {{ $country->renewal_start == 'PUB' ? 'selected' : '' }}>{{ __('Published') }}</option>
        </select>
      </td>
    </tr>
  </table>

  <div class="card mt-3">
    <div class="card-header">
      {{ __('Regional Phase Preselection') }}
      <small class="form-text text-muted">{{ __('These are not the official countries but the countries you chose as a preselection for the national phases') }}</small>
    </div>
    <div class="card-body p-2">
      <div class="row g-3">
        <div class="col-6 col-md-3">
          <input type="checkbox" class="btn-check noformat" name="ep" id="ep" {{ $country->ep == 1 ? 'checked' : '' }}>
          <label class="btn btn-outline-primary btn-sm w-100" for="ep">
            <i class="fas fa-euro-sign me-1"></i>EP
          </label>
        </div>
        <div class="col-6 col-md-3">
          <input type="checkbox" class="btn-check noformat" name="wo" id="wo" {{ $country->wo == 1 ? 'checked' : '' }}>
          <label class="btn btn-outline-primary btn-sm w-100" for="wo">
            <i class="fas fa-globe me-1"></i>PCT
          </label>
        </div>
        <div class="col-6 col-md-3">
          <input type="checkbox" class="btn-check noformat" name="em" id="em" {{ $country->em == 1 ? 'checked' : '' }}>
          <label class="btn btn-outline-primary btn-sm w-100" for="em">
            <i class="fas fa-registered me-1"></i>EM
          </label>
        </div>
        <div class="col-6 col-md-3">
          <input type="checkbox" class="btn-check noformat" name="oa" id="oa" {{ $country->oa == 1 ? 'checked' : '' }}>
          <label class="btn btn-outline-primary btn-sm w-100" for="oa">
            <i class="fas fa-map-marked-alt me-1"></i>OA
          </label>
        </div>
      </div>
    </div>
  </div>

  @if(!$country->numcode)
    {{-- Delete button for custom countries only --}}
    <div class="mt-3 d-flex justify-content-end">
      <button type="button" class="btn btn-outline-danger btn-sm" id="deleteCountry" data-message="{{ __('country') }} {{ $country->getTranslation('name', app()->getLocale(), false) ?: $country->getTranslation('name', 'en', false) ?: $country->iso }}" data-url="/countries/{{ $country->iso }}">
        <svg width="16" height="16" fill="currentColor" class="me-1">
          <use xlink:href="#trash"/>
        </svg>
        {{ __('Delete Country') }}
      </button>
    </div>
  @endif
</div>
@else
<div class="alert alert-warning">
    {{ __('You need administrative privileges to edit country information') }}
</div>
@endcan
