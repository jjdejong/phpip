<div data-resource="/template-member/{{ $templateMember->id }}">
  <table class="table">
    <tr>
      <th>Name</th>
      <td><input type="text" class="form-control noformat" name="class_id" data-ac="/template-class/autocomplete" value="{{ is_null($templateMember->class) ? "" : $templateMember->class->name }}"></td>
      <th><label title="{{ $tableComments['language_id'] }}">Language</label></th>
      <td><input class="form-control noformat" type='text' data-ac="language/autocomplete" name="language_id" value="{{ $templateMember->language->language }}">
    </tr>
    <tr>
      <th><label title="{{ $tableComments['style_id'] }}">Style</label></th>
      <td><input type="text" class="form-control noformat" name="style_id" data-ac="/template-style/autocomplete" value="{{ is_null($templateMember->style) ? "" : $templateMember->style->style }}"></td>
      <th><label title="{{ $tableComments['format'] }}">Format</label></th>
      <td>
        <select name="format" class="noformat">
          <option value="TEXT" {{ $templateMember->format == "TEXT" ? 'selected' : ""}}>Text</option>
          <option value="HTML" {{ $templateMember->format == "HTML" ? 'selected' : ""}}>HTML</option>
        </select>
      </td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['summary'] }}">Name</label></th>
      <td colspan="3"><input type="text" class="form-control noformat" name="summary" value="{{ $templateMember->summary }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['subject'] }}">Subject</label></th>
      <td colspan="3"><input type="text" class="form-control noformat" name="subject" value="{{ $templateMember->subject }}"></td>
    </tr>
    <tr>
      <th><label title="{{ $tableComments['body'] }}">Body</label></th>
      <td colspan="3"><textarea class="form-control noformat" name="body" value="{{ $templateMember->body }}" rows="20">{{ $templateMember->body }}</textarea></td>
    </tr>
  </table>
  <button type="button" class="btn btn-danger" title="Delete template" id="deleteMember" data-message="the template {{ $templateMember->class->name  }}" data-url='/template-member/{{ $templateMember->id }}'>
    Delete
  </button>
</div>
