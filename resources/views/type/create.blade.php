<form id="createTypeForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>{{ _i('Code') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="type" title="{{ $tableComments['type'] }}"><b>{{ _i('Type name') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="type"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createTypeSubmit" class="btn btn-primary">{{ _i('Create type') }}</button><br>
</form>
