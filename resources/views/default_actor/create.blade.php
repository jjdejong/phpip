<form id="createDActorForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
            <td><label title="{{ $tableComments['actor_id'] }}">Actor</label></td>
            <td><input type="hidden" name="actor_id">
            <input type="text" class="form-control form-control-sm" placeholder="Actor" data-ac="/actor/autocomplete" data-actarget="actor_id"></td>
            <td><label title="{{ $tableComments['role'] }}">Role</label></td>
            <td><input type="hidden" name="role">
          <input type="text" class="form-control form-control-sm" placeholder="Role" data-ac="/role/autocomplete" data-actarget="role"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_country'] }}">Country</label></td>
            <td><input type="hidden" name="for_country">
            <input type="text" class="form-control form-control-sm" placeholder="Country" data-ac="/country/autocomplete" data-actarget="for_country"></td>
            <td><label title="{{ $tableComments['for_category'] }}">Category</label></td>
            <td><input type="hidden" name="for_category">
          <input type="text" class="form-control form-control-sm" placeholder="Category" data-ac="/category/autocomplete" data-actarget="for_category"></td>
      </tr>
      <tr>
            <td><label title="{{ $tableComments['for_client'] }}">Client</label></td>
            <td><input type="hidden" name="for_client">
          <input type="text" class="form-control form-control-sm" placeholder="Client" data-ac="/actor/autocomplete" data-actarget="for_client"></td>
            <td><label title="{{ $tableComments['shared'] }}">Is shared</label></td>
          <td><input type="checkbox" class="form-control form-control-sm" value="1" name="shared"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createDActorSubmit" class="btn btn-primary">Create entry</button><br>
  <span id="zoneAlert" class="alert float-left"></span>
</form>
