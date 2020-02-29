<form id="createUserForm">
  <table class="table table-borderless">
    <tr>
      <td width="50%">
				<label class="required-field" title="{{ $userComments['name'] }}">Name *</label>
				<input class="form-control" name="name" placeholder="NAME Firstname">
				<small class="form-text text-muted">
  				If the user exists in the actor table, first convert the actor to a user by supplying a login <a href="/actor">here</a>
				</small>
			</td>
      <td>
				<label title="{{ $userComments['login'] }}">Login *</label>
				<input class="form-control" name="login" autocomplete="off">
			</td>
    </tr>
    <tr>
      <td>
				<label title="Min 8 characters with a-z, A-Z, 0-9 and special">Password *</label>
				<input type="password" class="form-control" name="password">
			</td>
      <td>
				<label>Confirm Password</label>
				<input type="password" class="form-control" name="password_confirmation">
			</td>
    </tr>
    <tr>
      <td>
				<label title="Select a DB role with the desired permissions">Role *</label>
				<input type="hidden" name="default_role">
				<input type="text" class="form-control" data-ac="/dbrole/autocomplete" data-actarget="default_role" data-aclength="0">
			</td>
      <td>
				<label title="Select user's company">Company</label>
				<input type="hidden"  name="company_id">
				<input type="text" class="form-control" data-ac="/actor/autocomplete" data-actarget="company_id">
			</td>
    </tr>
    <tr>
      <td>
				<label>Email *</label>
				<input type='text' class="form-control" name="email">
			</td>
      <td>
				<label>Phone</label>
				<input type='text' class="form-control" name="phone">
			</td>
    </tr>
  </table>
	<button type="button" id="createUserSubmit" class="btn btn-primary btn-block">Create</button>
</form>
