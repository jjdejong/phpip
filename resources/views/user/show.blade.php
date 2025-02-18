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
    </div>

    <div class="tab-pane fade" id="credentials">
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
    </div>
  </div>
</div>