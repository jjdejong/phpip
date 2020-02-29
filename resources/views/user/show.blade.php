<div class="card overflow-auto">
  <table class="table table-striped" data-resource="/user/{{ $userInfo->id }}">
    <tr>
      <th><label class="required-field" title="{{ $userComments['name'] }}">Name</label></th>
      <td><input class="noformat form-control" name="name" value="{{ $userInfo->name }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $userComments['login'] }}">Login</label></th>
      <td><input type="text" class="noformat form-control" name="login" value="{{ $userInfo->login }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $userComments['password'] }}">Password</label></th>
      <td><input type="password" class="noformat form-control" name="password" placeholder="***"></td>
    </tr>
    <tr>
      <th><label title="{{ $userComments['default_role'] }}">Role</label></th>
      <td><input type="text" class="noformat form-control" name="default_role" data-ac="/dbrole/autocomplete" data-aclength="0" value="{{ empty($userInfo->roleInfo) ? '' : $userInfo->roleInfo->name }}" autocomplete="off"></td>
    </tr>
    <tr>
      <th>Email</th>
      <td><input type='text' class="noformat form-control" name="email" value="{{ $userInfo->email }}" required></td>
    </tr>
    <tr>
      <th><label title="{{ $userComments['company_id'] }}">Company</label></th>
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
