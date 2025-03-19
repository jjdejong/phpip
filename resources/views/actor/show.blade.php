<div class="card" style="height: 480px;">
  <nav class="nav nav-tabs nav-fill">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#actorMain">{{ __('Main') }}</button>
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#actorContact">{{ __('Contact') }}</button>
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#actorOther">{{ __('Other') }}</button>
    <a class="nav-link" data-bs-toggle="tab" id="actorUsedInToggle" href="/actor/{{ $actorInfo->id }}/usedin" data-bs-target="#actorUsedIn">{{ __('Used in') }}</a>
    @can('readwrite')
    <button id="deleteActor" title="{{ __('Delete actor') }}" class="nav-link btn btn-outline-danger" data-url='/actor/{{ $actorInfo->id }}' data-message="{{ __('the actor') }} {{ $actorInfo->name }}">
      {{ __('Delete') }}
    </button>
    @endcan
  </nav>
  <div class="tab-content p-1" data-resource="/actor/{{ $actorInfo->id }}">
    <fieldset class="tab-pane fade show active" id="actorMain">
      <table class="table table-striped table-sm">
        <tr>
          <th title="{{ $actorComments['name'] }}">{{ __('Name') }}</th>
          <td><input class="noformat form-control" name="name" value="{{ $actorInfo->name }}"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['first_name'] }}">{{ __('First name') }}</th>
          <td><input class="noformat form-control" name="first_name" value="{{ $actorInfo->first_name }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['display_name'] }}">{{ __('Display name') }}</th>
          <td><input type="text" class="noformat form-control" name="display_name" value="{{ $actorInfo->display_name }}" placeholder="-"></td>
        </tr>
        <tr>
          <th>{{ __('Address') }}</th>
          <td><textarea class="noformat form-control" name="address">{{ $actorInfo->address }}</textarea></td>
        </tr>
        <tr>
          <th>{{ __('Country') }}</th>
          <td><input type='text' class="noformat form-control" name="country" data-ac="/country/autocomplete" value="{{ empty($actorInfo->countryInfo) ? '' : $actorInfo->countryInfo->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th>{{ __('Nationality') }}</th>
          <td><input type="text" class="noformat form-control" name="nationality" data-ac="/country/autocomplete" value="{{ empty($actorInfo->nationalityInfo) ? '' : $actorInfo->nationalityInfo->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th>{{ __('Language') }}</th>
          <td><input type="text" class="noformat form-control" name="language" placeholder="fr/en/de" value="{{ $actorInfo->language }}" autocomplete="off"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['function'] }}">{{ __('Function') }}</th>
          <td><input type="text" class="noformat form-control" name="function" value="{{ $actorInfo->function }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['company_id'] }}">{{ __('Employer') }}</th>
          <td><input type="text" class="noformat form-control" name="company_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->company) ? '' : $actorInfo->company->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['phy_person'] }}">{{ __('Physical Person') }}</td>
          <td><input type="checkbox" class="noformat" name="phy_person" {{ $actorInfo->phy_person ? 'checked' : '' }}></th>
        </tr>
        <tr>
          <th title="{{ $actorComments['small_entity'] }}">{{ __('Small Entity') }}</th>
          <td><input type="checkbox" class="noformat" name="small_entity" {{ $actorInfo->small_entity ? 'checked' : '' }}></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="tab-pane fade" id="actorContact">
      <table class="table table-striped">
        <tr>
          <th>{{ __('Address mailing') }}</th>
          <td><textarea class="noformat form-control" name="address_mailing">{{ $actorInfo->address_mailing }}</textarea></td>
        </tr>
        <tr>
          <th>{{ __('Country mailing') }}</th>
          <td><input type='text' class="noformat form-control" name="country_mailing" data-ac="/country/autocomplete" value="{{ empty($actorInfo->country_mailingInfo ) ? '' : $actorInfo->country_mailingInfo->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th>{{ __('Address billing') }}</th>
          <td><textarea class="noformat form-control" name="address_billing">{{ $actorInfo->address_billing }}</textarea></td>
        </tr>
        <tr>
          <th>{{ __('Country billing') }}</th>
          <td><input class="noformat form-control" name="country_billing" data-ac="/country/autocomplete" value="{{ empty($actorInfo->country_billingInfo ) ? '' : $actorInfo->country_billingInfo->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th>{{ __('Email') }}</th>
          <td><input type='email' class="noformat form-control" name="email" value="{{ $actorInfo->email }}" placeholder="-"></td>
        </tr>
        <tr>
          <th>{{ __('Phone') }}</th>
          <td><input type='text' class="noformat form-control" name="phone" value="{{ $actorInfo->phone }}" placeholder="-"></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="tab-pane fade" id="actorOther">
      <table class="table table-striped">
        <tr>
          <th title="{{ $actorComments['login'] }}">{{ __('Login') }}</th>
          <td><input type="text" class="noformat form-control" name="login" value="{{ $actorInfo->login }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['default_role'] }} Login needs to be null for changing the role">{{ __('Default role') }}</th>
          <td><input type="text" class="noformat form-control" name="default_role" data-ac="/role/autocomplete" value="{{ empty($actorInfo->droleInfo) ? '' : $actorInfo->droleInfo->name }}" {{ $actorInfo->login ? 'disabled' : 'autocomplete=off' }} placeholder="-"></td>
        </tr>
        <tr>
          <th>
            <div class="mb-0" title="{{ $actorComments['ren_discount'] }}">{{ __('Discount for renewals') }}</div>
            <div class="form-text text-muted">
              {{ __('Enter a multiplier rate (e.g. 0.5) or a fixed fee (e.g. 150)') }}
            </div>
          </th>
          <td><input type="text" class="noformat form-control" name="ren_discount" value="{{ $actorInfo->ren_discount ? $actorInfo->ren_discount : '' }}" placeholder="{{ __('Fixed fee or rate') }}"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['warn'] }}">{{ __('Warn') }}</th>
          <td><input type="checkbox" class="noformat" name="warn" {{ $actorInfo->warn ? 'checked' : '' }}></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['legal_form'] }}">{{ __('Legal form') }}</th>
          <td><input type='text' class="noformat form-control" name="legal_form" value="{{ $actorInfo->legal_form }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['registration_no'] }}">{{ __('Registration no.') }}</th>
          <td><input type='text' class="noformat form-control" name="registration_no" value="{{ $actorInfo->registration_no }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['VAT_number'] }}">{{ __('VAT no.') }}</th>
          <td><input type='text' class="noformat form-control" name="VAT_number" value="{{ $actorInfo->VAT_number }}" placeholder="-"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['parent_id'] }}">{{ __('Parent company') }}</th>
          <td><input type="text" class="noformat form-control" name="parent_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->parent) ? '' : $actorInfo->parent->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
        <tr>
          <th title="{{ $actorComments['site_id'] }}">{{ __('Work site') }}</th>
          <td><input type="text" class="noformat form-control" name="site_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->site) ? '' : $actorInfo->site->name }}" placeholder="-" autocomplete="off"></td>
        </tr>
      </table>
    </fieldset>
    <div class="tab-pane fade" id="actorUsedIn">
      <div class="spinner-border" role="status"></div>
    </div>
    <div>
      <label class="form-label fw-bolder" title="{{ $actorComments['notes'] }}">{{ __('Notes') }}</label>
      <textarea class="noformat form-control" name="notes">{{ $actorInfo->notes }}</textarea>
    </div>
  </div>
</div>
