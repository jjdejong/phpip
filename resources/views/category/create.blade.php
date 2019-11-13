<form id="createCategoryForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>Code</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="category" title="{{ $tableComments['category'] }}"><b>Category name</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="category"></td>
      </tr>
      <tr>
        <td><label for="ref_prefix" title="{{ $tableComments['ref_prefix'] }}">Reference prefix</label></td>
        <td><input type="text" class="form-control form-control-sm" name="ref_prefix"></td>
        <td><label for="display_with" title="{{ $tableComments['display_with'] }}"><b>Display with</b></label></td><td>
          <input type='hidden' name='display_with'>
          <input type="text" class="form-control form-control-sm" list="ajaxDatalist" data-ac="/category/autocomplete" data-actarget="display_with" autocomplete="off">
        </td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createCategorySubmit" class="btn btn-primary">Create category</button><br>
  <span id="zoneAlert" class="alert float-left"></span>
</form>
