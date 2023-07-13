<form id="createUserForm">
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="{{ $userComments['name'] }}">Name</label>
				<input class="form-control" name="name" placeholder="NAME Firstname">
				<small class="form-text text-muted">
					If the user exists in the actor table, first convert the actor to a user by supplying a login <a
						href="/actor">here</a>
				</small>
			</div>
			<div class="col">
				<label class="fw-bolder" title="{{ $userComments['login'] }}">Login</label>
				<input class="form-control" name="login" autocomplete="off">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="Min 8 characters with a-z, A-Z, 0-9 and special">Password</label>
				<input type="password" class="form-control" name="password">
			</div>
			<div class="col">
				<label>Confirm Password</label>
				<input type="password" class="form-control" name="password_confirmation">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="Select a DB role with the desired permissions">Role</label>
				<input type="hidden" name="default_role">
				<input type="text" class="form-control" data-ac="/dbrole/autocomplete" data-actarget="default_role"
					data-aclength="0">
			</div>
			<div class="col">
				<label title="Select user's company">Company</label>
				<input type="hidden" name="company_id">
				<input type="text" class="form-control" data-ac="/actor/autocomplete" dπata-actarget="company_id">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder">Email</label>
				<input type='text' class="form-control" name="email">
			</div>
			<div class="col">
				<label>Phone</label>
				<input type='text' class="form-control" name="phone">
			</div>
		</div>
	<div class="d-grid">
		<button type="button" id="createUserSubmit" class="btn btn-primary">Create</button>
	</div>
</form>