<form id="createMemberForm">
  <fieldset>
    <legend>New template</legend>
    <table class="table table-sm">
      <tr>
        <td><label for="name" title="{{ $tableComments['class_id'] }} "><b>Name</b></label></td>
        <td >
          <input type='hidden' name='class_id'>
          <input type="text" class="form-control form-control-sm" data-ac="/template-class/autocomplete"  data-actarget="class_id">
        </td>
        <td><label for="description" title="{{ $tableComments['language_id'] }}"><b>Language</b></label></td>
        <td >
          <input type='hidden' name='language_id'>
          <input type="text" class="form-control form-control-sm" data-ac="/language/autocomplete"  data-actarget="language_id">
        </td>
      </tr>
      <tr>
        <td><label for="style" title="{{ $tableComments['style_id'] }}">Style</label></td>
        <td>
          <input type='hidden' name='style_id'>
          <input type="text" class="form-control form-control-sm" data-ac="/template-style/autocomplete" data-actarget="style_id" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['format'] }}">Format</label></td>
        <td>
          <select name="format" class="noformat">
            <option value="TEXT" >Text</option>
            <option value="HTML" >HTML</option>
          </select>
        </td>
      </tr>
      <tr>
        <th><label title="{{ $tableComments['subject'] }}">Subject</label></th>
        <td colspan="3"><input type="text" class="form-control noformat" name="subject"></td>
      </tr>
      <tr>
        <th><label title="{{ $tableComments['body'] }}">Body</label></th>
        <td colspan="3"><textarea class="form-control noformat" name="body" rows="10"></textarea></td>
      </tr>
    </table>
  </fieldset>

  <button type="button" class="btn btn-danger" id="createMemberSubmit" data-redirect="/template-member">Create template</button><br>
</form>
