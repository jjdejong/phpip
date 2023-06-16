<form id="createCategoryForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ _i($tableComments['code']) }}"><b>{{ _i("Code")}}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="category" title="{{ _i($tableComments['category']) }}"><b>{{ _i("Category name")}}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="category"></td>
      </tr>
      <tr>
        <td><label for="ref_prefix" title="{{ _i($tableComments['ref_prefix']) }}">{{ _i("Reference prefix")}}</label></td>
        <td><input type="text" class="form-control form-control-sm" name="ref_prefix"></td>
        <td><label for="display_with" title="{{ _i($tableComments['display_with']) }}"><b>{{ _i("Display with")}}</b></label></td><td>
          <input type='hidden' name='display_with'>
          <input type="text" class="form-control form-control-sm" data-ac="/category/autocomplete" data-actarget="display_with" autocomplete="off">
        </td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createCategorySubmit" class="btn btn-primary">{{ _i("Create category")}}</button><br>
</form>
