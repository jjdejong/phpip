<form id="createMemberForm">
  <fieldset>
    <legend>{{ _i('New template') }}</legend>
    <table class="table table-sm">
      <tr>
        <td><label for="name" title="{{ $tableComments['class_id'] }} "><b>{{ _i('Class') }}</b></label></td>
        <td >
          <input type='hidden' name='class_id'>
          <input type="text" class="form-control form-control-sm" data-ac="/template-class/autocomplete"  data-actarget="class_id">
        </td>
        <td><label for="description" title="{{ $tableComments['language'] }}"><b>{{ _i('Language') }}</b></label></td>
        <td >
          <select name="language">
            @foreach ($languages as $code => $lang_name)
              <option value='{{ $code }}'  {{ $code == 'en'  ? 'selected' : ""}}>{{ $lang_name}}
            @endforeach
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="style" title="{{ $tableComments['style'] }}">{{ _i('Style') }}</label></td>
        <td>
          <input type="text" class="form-control form-control-sm"  name='style' data-ac="/template-style/autocomplete" data-actarget="style" autocomplete="off">
        </td>
        <td><label title="{{ $tableComments['format'] }}">{{ _i('Format') }}</label></td>
        <td>
          <select name="format">
            <option value="TEXT" >{{ _i('Text') }}</option>
            <option value="HTML" >HTML</option>
          </select>
        </td>
      </tr>
      <tr>
        <th><label title="{{ $tableComments['format'] }}">{{ _i('Summary') }}</label></th>
        <td><input type="text" class="form-control" name="summary"></td>
        <td><label for="category" title="{{ $tableComments['category'] }}">{{ _i('Category') }}</label></td>
        <td>
          <input type="text"  name='category' class="form-control form-control-sm" data-ac="/template-category/autocomplete" data-actarget="category" autocomplete="off">
        </td>
      </tr>
      <tr>
        <th><label title="{{ $tableComments['subject'] }}">{{ _i('Subject') }}</label></th>
        <td colspan="3"><textarea class="form-control" name="subject"></textarea></td>
      </tr>
      <tr>
        <th><label title="{{ $tableComments['body'] }}">{{ _i('Body') }}</label></th>
        <td colspan="3"><textarea class="form-control" name="body" rows="20"></textarea></td>
      </tr>
    </table>
  </fieldset>

  <button type="button" class="btn btn-danger" id="createMemberSubmit" data-redirect="/template-member">{{ _i('Create template') }}</button><br>
</form>
