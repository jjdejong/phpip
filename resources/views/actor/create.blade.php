<form id="createActorForm">
	<fieldset>
    <table class="table table-borderless">
      <tr>
        <td><label for="name" class="required-field" title="{{ _i($actorComments['name']) }}">{{ _i("Name *") }}</label></td>
        <td><input class="form-control" name="name" placeholder="{{ _i('NAME Firstname') }}"></td>
        <td><label for="first_name" title="{{ _i($actorComments['first_name']) }}">{{ _i("First name") }}</label></td>
        <td><input class="form-control" name="first_name" placeholder="{{ _i('Optional') }}"></td>
      </tr>
      <tr>
        <td><label for="display_name" title="{{ $actorComments['display_name'] }}">{{ _i("Display name") }}</label></td>
        <td><input type="text" class="form-control" name="display_name"></td>
				<td><label for="company_id" title="{{ _i($actorComments['company_id']) }}">{{ _i("Employer") }}</label></td>
        <td>
					<input type="hidden"  name="company_id">
					<input type="text" class="form-control" data-ac="/actor/autocomplete" data-actarget="company_id" autocomplete="off">
				</td>
      </tr>
      <tr>
        <td><label for="default_role" title="{{ _i($actorComments['default_role']) }}">{{ _i("Default role") }}</label></td>
        <td>
					<input type="hidden" name="default_role">
					<input type="text" class="form-control" data-ac="/role/autocomplete" data-actarget="default_role" autocomplete="off">
				</td>
        <td><label for="function" title="{{ _i($actorComments['function']) }}">{{ _i("Function") }}</label></td>
        <td><input class="form-control" name="function"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{{ _i("Contact details") }}</legend>
    <table class="table table-borderless">
      <tr>
        <td><label for="address">{{ _i("Address") }}</label></td>
        <td><textarea class="form-control" name="address"></textarea></td>
        <td><label for="country">{{ _i("Country") }}</label></td>
        <td>
					<input type="hidden" name="country">
					<input type='text' class="form-control" data-ac="/country/autocomplete" data-actarget="country" autocomplete="off">
				</td>
      </tr>
      <tr>
        <td><label for="email">{{ _i("Email") }}</label></td>
        <td><input type='text' class="form-control" name="email"></td>
        <td><label for="phone">{{ _i("Phone") }}</label></td>
        <td><input type='text' class="form-control" name="phone"></td>
      </tr>
    </table>
  </fieldset>
	<button type="button" id="createActorSubmit" class="btn btn-primary btn-block">{{ _i("Create") }}</button>
</form>
