<form id="createUserForm">
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="{{ $userComments['name'] }}">{{ __('Name') }}</label>
				<input class="form-control" name="name" placeholder="{{ __('NAME Firstname') }}">
				<small class="form-text text-muted">
					{{ __('If the user exists in the actor table, first convert the actor to a user by supplying a login') }} <a
						href="/actor">{{ __('here') }}</a>
				</small>
			</div>
			<div class="col">
				<label class="fw-bolder" title="{{ $userComments['login'] }}">{{ __('User name') }}</label>
				<input class="form-control" name="login" autocomplete="off">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="{{ __('Min 8 characters with a-z, A-Z, 0-9 and special') }}">{{ __('Password') }}</label>
				<input type="password" class="form-control" name="password">
			</div>
			<div class="col">
				<label>{{ __('Confirm Password') }}</label>
				<input type="password" class="form-control" name="password_confirmation">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder" title="{{ __('Select a DB role with the desired permissions') }}">{{ __('Role') }}</label>
				<input type="hidden" name="default_role">
				<input type="text" class="form-control" data-ac="/dbrole/autocomplete" data-actarget="default_role"
					data-aclength="0">
			</div>
			<div class="col">
				<label title="{{ __('Select user\'s company') }}">{{ __('Company') }}</label>
				<input type="hidden" name="company_id">
				<input type="text" class="form-control" data-ac="/actor/autocomplete" dπata-actarget="company_id">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col">
				<label class="fw-bolder">{{ __('Email') }}</label>
				<input type='text' class="form-control" name="email">
			</div>
			<div class="col">
				<label>{{ __('Phone') }}</label>
				<input type='text' class="form-control" name="phone">
			</div>
		</div>
	<div class="d-grid">
		<button type="button" id="createUserSubmit" class="btn btn-primary">{{ __('Create') }}</button>
	</div>
</form>