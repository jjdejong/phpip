<form id="createCategoryForm">
  <fieldset>
    <table class="table table-sm">
      <tr>
        <td><label for="code" title="{{ $tableComments['code'] }}"><b>{{ __('Code') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="code"></td>
        <td><label for="category" title="{{ $tableComments['category'] }}"><b>{{ __('Category name') }}</b></label></td>
        <td><input type="text" class="form-control form-control-sm" name="category"></td>
      </tr>
      <tr>
        <td><label for="ref_prefix" title="{{ $tableComments['ref_prefix'] }}">{{ __('Reference prefix') }}</label></td>
        <td><input type="text" class="form-control form-control-sm" name="ref_prefix"></td>
        <td><label for="display_with" title="{{ $tableComments['display_with'] }}"><b>{{ __('Display with') }}</b></label></td><td>
          <input type='hidden' name='display_with'>
          <input type="text" class="form-control form-control-sm" data-ac="/category/autocomplete" data-actarget="display_with" autocomplete="off">
        </td>
      </tr>
    </table>
  </fieldset>
  <button type="button" id="createCategorySubmit" class="btn btn-primary">{{ __('Create category') }}</button><br>
</form>
