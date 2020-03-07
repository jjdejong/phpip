
<script type="text/javascript">

// Manage filters input fields in Emil template selection box
filterTemplate.addEventListener('input', debounce( e => {
  if (e.target.value.length === 0) {
    url.searchParams.delete(e.target.name);
  } else {
    url.searchParams.set(e.target.name, e.target.value);
  }
    window.history.pushState('', 'phpIP', url);
    reloadPart(window.location.pathname, 'tableList');
}, 500));
</script>

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
  <table id="tableList">
      <thead  class="thead-light">
        <tr id="filterTemplate">
          <th class="col-2">
            <input class="form-control" name="Language" value="{{ old('Language')}}" placeholder="Language">
          </th>
          <th class="col-2">
            <input class="form-control" title="{{ $tableComments['category_id'] }}" name="Category" value="{{ old('Category')}}" placeholder="Category">
          </th>
          <th class="col-4">
            <input class="form-control" name="Name" value="{{ old('Name')}}" placeholder="Name">
          </th>
          <th class="col-2">
            <input class="form-control" title="{{ $tableComments['style_id'] }}" name="Style" value="{{ old('Style')}}" placeholder="Style">
          </th>
          <th class="col-1">
              Action
          </th>
        </tr>
      </thead>
      @foreach ($members as $member)
        <tr class="reveal-hidden" data-resource="/document/mailto/{{ $member->id }}">
          <td class = "col-2">
            {{ $member->language->language }}
          </td>
          <td class = "col-2">
            {{ $member->category->category }}
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
  </table>
</form>
