<form id="createActorForm">
	<fieldset>
    <table class="table table-borderless">
      <tr>
        <td><label for="name" class="required-field" title="{{ __($actorComments['name']) }}">{{ _i("Name *") }}</label></td>
        <td><input class="form-control" name="name" placeholder="{{ __('NAME Firstname') }}"></td>
        <td><label for="first_name" title="{{ __($actorComments['first_name']) }}">{{ _i("First name") }}</label></td>
        <td><input class="form-control" name="first_name" placeholder="{{ __('Optional') }}"></td>
      </tr>
      <tr>
        <td><label for="display_name" title="{{ $actorComments['display_name'] }}">{{ __("Display name") }}</label></td>
        <td><input type="text" class="form-control" name="display_name"></td>
				<td><label for="company_id" title="{{ __($actorComments['company_id']) }}">{{ _i("Employer") }}</label></td>
        <td>
					<input type="hidden"  name="company_id">
					<input type="text" class="form-control" data-ac="/actor/autocomplete" data-actarget="company_id" autocomplete="off">
				</td>
      </tr>
      <tr>
        <td><label for="default_role" title="{{ __($actorComments['default_role']) }}">{{ _i("Default role") }}</label></td>
        <td>
					<input type="hidden" name="default_role">
					<input type="text" class="form-control" data-ac="/role/autocomplete" data-actarget="default_role" autocomplete="off">
				</td>
        <td><label for="function" title="{{ __($actorComments['function']) }}">{{ _i("Function") }}</label></td>
        <td><input class="form-control" name="function"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{{ __("Contact details") }}</legend>
    <table class="table table-borderless">
      <tr>
        <td><label for="address">{{ __("Address") }}</label></td>
        <td><textarea class="form-control" name="address"></textarea></td>
        <td><label for="country">{{ __("Country") }}</label></td>
        <td>
					<input type="hidden" name="country">
					<input type='text' class="form-control" data-ac="/country/autocomplete" data-actarget="country" autocomplete="off">
				</td>
      </tr>
      <tr>
        <td><label for="email">{{ __("Email") }}</label></td>
        <td><input type='text' class="form-control" name="email"></td>
        <td><label for="phone">{{ __("Phone") }}</label></td>
        <td><input type='text' class="form-control" name="phone"></td>
      </tr>
    </table>
  </fieldset>
	<button type="button" id="createActorSubmit" class="btn btn-primary">Create</button>
</form>
