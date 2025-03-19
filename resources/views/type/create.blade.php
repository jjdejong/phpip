<form id="createTypeForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>{{ __('Code') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="type" title="{{ $tableComments['type'] }}"><b>{{ __('Type name') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="type"></td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createTypeSubmit" class="btn btn-primary">{{ __('Create type') }}</button><br>
</form>
