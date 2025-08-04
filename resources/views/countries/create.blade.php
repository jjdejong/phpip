<form id="createCountryForm" class="needs-validation" novalidate>
  
  <div class="mb-3">
    <label for="iso" class="form-label">{{ __('ISO Code') }} <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="iso" name="iso" maxlength="2" pattern="[A-Z]{2}" required>
    <div class="form-text">{{ __('2-letter ISO country code (e.g., XX)') }}</div>
    <div class="invalid-feedback">{{ __('Please enter a valid 2-letter ISO code') }}</div>
  </div>

  <div class="mb-3">
    <label for="name_en" class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name_en" name="name[en]" required>
    <div class="invalid-feedback">{{ __('Please enter the English name') }}</div>
  </div>

  <div class="mb-3">
    <label for="name_fr" class="form-label">{{ __('Name (French)') }}</label>
    <input type="text" class="form-control" id="name_fr" name="name[fr]">
  </div>

  <div class="mb-3">
    <label for="name_de" class="form-label">{{ __('Name (German)') }}</label>
    <input type="text" class="form-control" id="name_de" name="name[de]">
  </div>

  <div class="mb-3">
    <label for="renewal_base" class="form-label">{{ __('Base renewal') }}</label>
    <select class="form-select" id="renewal_base" name="renewal_base">
      <option value="">{{ __('None') }}</option>
      <option value="FIL">{{ __('Filed') }}</option>
      <option value="GRT">{{ __('Granted') }}</option>
      <option value="PUB">{{ __('Published') }}</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="renewal_first" class="form-label">{{ __('First renewal') }}</label>
    <input type="number" class="form-control" id="renewal_first" name="renewal_first" min="1">
  </div>

  <div class="mb-3">
    <label for="renewal_start" class="form-label">{{ __('Start from') }}</label>
    <select class="form-select" id="renewal_start" name="renewal_start">
      <option value="">{{ __('None') }}</option>
      <option value="FIL">{{ __('Filed') }}</option>
      <option value="GRT">{{ __('Granted') }}</option>
      <option value="PUB">{{ __('Published') }}</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">{{ __('Regional Phase Preselection') }}</label>
    <div class="row g-2">
      <div class="col-6">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="ep" id="ep" value="1">
          <label class="form-check-label" for="ep">{{ __('EP Member') }}</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="wo" id="wo" value="1">
          <label class="form-check-label" for="wo">{{ __('PCT Member') }}</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="em" id="em" value="1">
          <label class="form-check-label" for="em">{{ __('EM Member') }}</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="oa" id="oa" value="1">
          <label class="form-check-label" for="oa">{{ __('OAPI Member') }}</label>
        </div>
      </div>
    </div>
  </div>

  <div class="d-grid">
    <button type="button" id="createCountrySubmit" class="btn btn-primary">{{ __('Create Country') }}</button>
  </div>
</form>

<script>
// Bootstrap form validation
(function() {
  'use strict';
  const form = document.querySelector('.needs-validation');
  form.addEventListener('submit', function(event) {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);

  // Auto-uppercase ISO code
  const isoInput = document.getElementById('iso');
  isoInput.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
  });
})();
</script>