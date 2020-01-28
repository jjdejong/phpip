<div class="card overflow-auto">
  <table class="table table-striped table-sm" data-resource="/user/{{ $userInfo->id }}">
    <tr>
      <td><label for="name" class="required-field" title="{{ $userComments['name'] }}">Name</label> </td>
      <td><input class="noformat form-control" name="name" value="{{ $userInfo->name }}"></td>
    </tr>
    <tr>
      <td><label for="login" title="{{ $userComments['login'] }}">Login</label></td>
      <td><input type="text" class="noformat form-control" name="login" value="{{ $userInfo->login }}"></td>
    </tr>
    <tr>
      <td><label for="password" title="{{ $userComments['password'] }}">Password</label></td>
      <td><input type="password" class="noformat form-control" name="password" placeholder="***"></td>
    </tr>
    <tr>
      <td><label for="default_role" title="{{ $userComments['default_role'] }}">Role</label></td>
      <td><input type="text" class="noformat form-control" name="default_role" data-ac="/dbrole/autocomplete" value="{{ empty($userInfo->roleInfo) ? '' : $userInfo->roleInfo->name }}" autocomplete="off"></td>
    </tr>
    <tr>
      <td><label for="email">Email</label></td>
      <td><input type='text' class="noformat form-control" name="email" value="{{ $userInfo->email }}" required></td>
    </tr>
    <tr>
      <td><label for="company_id" title="{{ $userComments['company_id'] }}">Company</label></td>
      <td><input type="text" class="noformat form-control" name="company_id" data-ac="/actor/autocomplete" value="{{ empty($userInfo->company) ? '' : $userInfo->company->name }}" autocomplete="off"></td>
    </tr>
    <tr>
      <td><label for="phone">Phone</label></td>
      <td><input type='text' class="noformat form-control" name="phone" value="{{ $userInfo->phone }}"></td>
    </tr>
    <tr>
      <td><label for="notes" title="{{ $userComments['notes'] }}">Notes</label></td>
      <td><textarea class="noformat form-control" name="notes">{{ $userInfo->notes }}</textarea></td>
    </tr>
  </table>
</div>
