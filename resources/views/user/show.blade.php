<div class="{{ isset($isProfileView) && $isProfileView ? 'row' : '' }}">
  <div class="{{ isset($isProfileView) && $isProfileView ? 'col-md-6' : '' }}">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title mb-0">User Info</h5>
      </div>
      <div class="card-body">
        <table class="table table-striped" data-resource="{{ isset($isProfileView) ? route('user.updateProfile') : '/user/' . $userInfo->id }}">
          <tr>
            <th title="{{ $userComments['name'] }}">Name</th>
            <td><input class="noformat form-control" name="name" value="{{ $userInfo->name }}"></td>
          </tr>
          <tr>
            <th title="{{ $userComments['default_role'] }}">Role</th>
            <td><input type="text" class="noformat form-control" name="default_role" data-ac="/dbrole/autocomplete" data-aclength="0" value="{{ empty($userInfo->roleInfo) ? '' : $userInfo->roleInfo->name }}" autocomplete="off"></td>
          </tr>
          <tr>
            <th>Email</th>
            <td><input type='email' class="noformat form-control" name="email" value="{{ $userInfo->email }}" required></td>
          </tr>
          <tr>
            <th title="{{ $userComments['company_id'] }}">Company</th>
            <td><input type="text" class="noformat form-control" name="company_id" data-ac="/actor/autocomplete" value="{{ empty($userInfo->company) ? '' : $userInfo->company->name }}" autocomplete="off"></td>
          </tr>
          <tr>
            <th>Phone</th>
            <td><input type='text' class="noformat form-control" name="phone" value="{{ $userInfo->phone }}"></td>
          </tr>
          <tr>
            <th>Language</th>
            <td>
              <select class="form-select noformat" name="language" required>
                <option value="en_GB" {{ $userInfo->language == 'en_GB' ? 'selected' : '' }}>English (British)</option>
                <option value="en_US" {{ $userInfo->language == 'en_US' ? 'selected' : '' }}>English (American)</option>
                <option value="fr" {{ $userInfo->language == 'fr' ? 'selected' : '' }}>Français</option>
                <option value="de" {{ $userInfo->language == 'de' ? 'selected' : '' }}>Deutsch</option>
                <option value="es" {{ $userInfo->language == 'es' ? 'selected' : '' }}>Español</option>
              </select>
            </td>
          </tr>
          @if(!isset($isProfileView))
          <tr>
            <th>Notes</th>
            <td><textarea class="noformat form-control" name="notes">{{ $userInfo->notes }}</textarea></td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>

  <div class="{{ isset($isProfileView) && $isProfileView ? 'col-md-6' : '' }}">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Credentials</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ isset($isProfileView) ? route('user.updateProfile') : '/user/' . $userInfo->id }}">
          @csrf
          @method('PUT')
          <table class="table table-striped">
            <tr>
              <th>Login</th>
              <td>{{ $userInfo->login }}</td>
            </tr>
            <tr>
              <th>Password</th>
              <td>
                <input type="password" class="form-control" name="password" placeholder="Leave empty to keep password">
                @if(isset($isProfileView))
                <small class="text-muted">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</small>
                @endif
              </td>
            </tr>
            <tr>
              <th>Confirm Password</th>
              <td>
                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm password">
              </td>
            </tr>
          </table>
          <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>