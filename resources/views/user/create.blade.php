<form id="createUserForm" autocomplete="off">
  <table class="table table-borderless">
    <tr>
      <td>
				<label for="name" class="required-field" title="{{ $userComments['name'] }}">Name *</label>
				<input class="form-control" name="name" placeholder="NAME Firstname">
			</td>
      <td>
				<label for="login" title="{{ $userComments['login'] }}">Login *</label>
				<input class="form-control" name="login">
			</td>
    </tr>
    <tr>
      <td>
				<label for="password" title="{{ $userComments['password'] }}">Password (min 8 characters)</label>
				<input type="password" class="form-control" name="password">
			</td>
      <td>
				<label for="password_confirmation">Confirm Password</label>
				<input type="password" class="form-control" name="password_confirmation">
			</td>
    </tr>
    <tr>
      <td>
				<label for="default_role" title="{{ $userComments['default_role'] }}">Role *</label>
				<input type="hidden" name="default_role">
				<input type="text" class="form-control" data-ac="/dbrole/autocomplete" data-actarget="default_role">
			</td>
      <td>
				<label for="company_id" title="{{ $userComments['company_id'] }}">Company</label>
				<input type="hidden"  name="company_id">
				<input type="text" class="form-control" data-ac="/actor/autocomplete" data-actarget="company_id">
			</td>
    </tr>
    <tr>
      <td>
				<label for="email">Email *</label>
				<input type='text' class="form-control" name="email">
			</td>
      <td>
				<label for="phone">Phone</label>
				<input type='text' class="form-control" name="phone">
			</td>
    </tr>
  </table>
	<button type="button" id="createUserSubmit" class="btn btn-primary btn-block">Create</button>
</form>
