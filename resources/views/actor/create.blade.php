<form id="createActorForm">
	<fieldset>
    <table class="table table-hover table-sm">
      <tr>
        <td><label for="name" class="required-field" title="{{ $actorComments['name'] }}">Name</label></td>
        <td><input class="form-control" name="name"></td>
        <td><label for="first_name" title="{{ $actorComments['first_name'] }}">First name</label></td>
        <td><input class="form-control" name="first_name"></td>
      </tr>
      <tr>
        <td><label for="display_name" title="{{ $actorComments['display_name'] }}">Display name</label></td>
        <td><input type="text" class="form-control" name="display_name"></td>
        <td><label for="login" title="{{ $actorComments['login'] }}">Login</label></td>
        <td><input class="form-control" name="login"></td>
      </tr>
      <tr>
        <td><label for="default_role" title="{{ $actorComments['default_role'] }}">Default role</label></td>
        <td>
					<input type="hidden" name="default_role">
					<input type="text" class="form-control" list="ajaxDatalist" data-ac="/role/autocomplete" data-actarget="default_role" autocomplete="off">
				</td>
        <td><label for="function" title="{{ $actorComments['function'] }}">Function</label></td>
        <td><input class="form-control" name="function"></td>
      </tr>
      <tr>
        <td><label for="company_id" title="{{ $actorComments['company_id'] }}">Employer</label></td>
        <td>
					<input type="hidden"  name="company_id">
					<input type="text" class="form-control" list="ajaxDatalist" data-ac="/actor/autocomplete" data-actarget="company_id" autocomplete="off">
				</td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>Contact details</legend>
    <table class="table table-hover table-sm">
      <tr>
        <td><label for="address">Address</label></td>
        <td><textarea class="form-control" name="address"></textarea></td>
        <td><label for="country">Country</label></td>
        <td>
					<input type="hidden" name="country">
					<input type='text' class="form-control" list="ajaxDatalist" data-ac="/country/autocomplete" data-actarget="country" autocomplete="off">
				</td>
      </tr>
      <tr>
        <td><label for="email">Email</label></td>
        <td><input type='text' class="form-control" name="email"></td>
        <td><label for="phone">Phone</label></td>
        <td><input type='text' class="form-control" name="phone"></td>
      </tr>
    </table>
  </fieldset>
	<button type="button" id="createActorSubmit" class="btn btn-primary" data-dismiss="modal">Create</button>
</form>
