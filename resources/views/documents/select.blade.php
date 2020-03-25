<form id="sendDocumentForm">
  <input type='hidden' value="{{ $matter->id }}" name="matter_id">
  <fieldset>
    <legend>Documents for {{ $matter->uid}}</legend>
    <table>
    <tr>
      <td class="col-6">
        Contact
      </td>
      <td class="col-3">
        Send to:
      </td>
      <td class="col-3">
        CC:
      </td>
    </tr>
    @foreach ($contacts as $contact)
      <tr >
        <td class="col-6">
          {{ $contact->first_name}} {{ is_null($contact->name) ? $contact->display_name : $contact->name  }}
        </td>
        <td class="col-3">
          <input id="" class="contact" name="sendto-{{ $contact->actor_id }}" type="checkbox">
        </td>
        <td class="col-3">
          <input id="" class="contact" name="ccto-{{ $contact->actor_id }}" type="checkbox">
        </td>
      </tr>
    @endforeach
  </table>
  </fieldset>
  <table data-resource="/document/select/{{ $matter->id }}">
      <thead  class="thead-light">
        <tr>
          <th class="col-2">
            <input class="form-control filter" name="Language" value="{{ array_key_exists('Language', $oldfilters) ? $oldfilters['Language'] : "" }}" placeholder="Language">
          </th>
          <th class="col-2">
            <input class="form-control filter" name="Category" value="{{ array_key_exists('Category', $oldfilters) ? $oldfilters['Category'] : "" }}" placeholder="Category">
          </th>
          <th class="col-4">
            <input class="form-control filter" name="Name" value="{{ array_key_exists('Name', $oldfilters) ? $oldfilters['Name'] : "" }}" placeholder="Name">
          </th>
          <th class="col-2">
            <input class="form-control filter" title="{{ $tableComments['style_id'] }}" name="Style" value="{{ array_key_exists('Style', $oldfilters) ? $oldfilters['Style'] : "" }}" placeholder="Style">
          </th>
          <th class="col-1">
              Action
          </th>
        </tr>
      </thead>
      <tbody id="tableList" >
      @foreach ($members as $member)
        <tr class="reveal-hidden" data-resource="/document/mailto/{{ $member->id }}">
          <td class = "col-2">
            {{ $member->language->language }}
          </td>
          <td class = "col-2">
            {{ $member->class->category->category }}
          </td>

          <td class = "col-4">
            {{ $member->class->name }}
          </td>

          <td class = "col-2">
            {{ $member->style->style }}
          </td>
          <td class = "col-1">
            <a class="sendDocument btn btn-secondary btn-sm">Prepare</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</form>
