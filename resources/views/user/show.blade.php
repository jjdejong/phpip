
<div class="card">
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#userInfo">User Info</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#credentials">Credentials</a>
    </li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="userInfo">
      @if(isset($isProfileView) && $isProfileView)
      <table class="table table-striped">
        <tr>
          <th title="{{ $userComments['name'] }}">Name</th>
          <td>{{ $userInfo->name }}</td>
        </tr>
        <tr>
          <th title="{{ $userComments['default_role'] }}">Role</th>
          <td>{{ empty($userInfo->roleInfo) ? '' : $userInfo->roleInfo->name }}</td>
        </tr>
        <tr>
          <th>Email</th>
          <td>{{ $userInfo->email }}</td>
        </tr>
        <tr>
          <th title="{{ $userComments['company_id'] }}">Company</th>
          <td>{{ empty($userInfo->company) ? '' : $userInfo->company->name }}</td>
        </tr>
        <tr>
          <th>Phone</th>
          <td>{{ $userInfo->phone }}</td>
        </tr>
        <tr>
          <th>Language</th>
          <td>{{ $userInfo->language }}</td>
        </tr>
        <tr>
          <th>Last Login</th>
          <td>{{ $userInfo->last_login ? date('Y-m-d H:i', strtotime($userInfo->last_login)) : 'Never' }}</td>
        </tr>
      </table>
      @else
      <table class="table table-striped" data-resource="/user/{{ $userInfo->id }}">
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
          <td><input type='text' class="noformat form-control" name="email" value="{{ $userInfo->email }}" required></td>
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
          <th>Notes</th>
          <td><textarea class="noformat form-control" name="notes">{{ $userInfo->notes }}</textarea></td>
        </tr>
      </table>
      @endif
    </div>

    <div class="tab-pane fade" id="credentials">
      @if(isset($isProfileView) && $isProfileView)
      <form method="POST" action="{{ route('user.updateProfile') }}">
        @csrf
        @method('PUT')
        <table class="table table-striped">
          <tr>
            <th>Login</th>
            <td>{{ $userInfo->login }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>
              <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $userInfo->email) }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </td>
          </tr>
          <tr>
            <th>Language</th>
            <td>
              <select class="form-select @error('language') is-invalid @enderror" name="language" required>
                <option value="en_GB" {{ old('language', $userInfo->language) == 'en_GB' ? 'selected' : '' }}>English (British)</option>
                <option value="en_US" {{ old('language', $userInfo->language) == 'en_US' ? 'selected' : '' }}>English (American)</option>
                <option value="fr" {{ old('language', $userInfo->language) == 'fr' ? 'selected' : '' }}>Français</option>
                <option value="de" {{ old('language', $userInfo->language) == 'de' ? 'selected' : '' }}>Deutsch</option>
                <option value="es" {{ old('language', $userInfo->language) == 'es' ? 'selected' : '' }}>Español</option>
              </select>
              @error('language')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </td>
          </tr>
          <tr>
            <th>New Password</th>
            <td>
              <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Leave empty to keep current password">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</small>
            </td>
          </tr>
          <tr>
            <th>Confirm Password</th>
            <td>
              <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-end">
              <button type="submit" class="btn btn-primary">
                Update Profile
              </button>
            </td>
          </tr>
        </table>
      </form>
      @else
      <form id="credentialsForm" data-resource="/user/{{ $userInfo->id }}">
        <table class="table table-striped">
          <tr>
            <th title="{{ $userComments['login'] }}">Login</th>
            <td>
              <input type="text" class="form-control" name="login" value="{{ old('login', $userInfo->login) }}">
            </td>
          </tr>
          <tr>
            <th title="{{ $userComments['password'] }}">Password</th>
            <td>
              <input type="password" class="form-control" name="password" placeholder="New password">
            </td>
          </tr>
          <tr>
            <th>Confirm Password</th>
            <td>
              <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm password">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-end">
              <button type="submit" class="btn btn-primary" id="updateCredentials">
                Update Credentials
              </button>
            </td>
          </tr>
        </table>
      </form>
      @endif
    </div>
  </div>
</div>