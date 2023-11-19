<form id="sendDocumentForm">
  <input type='hidden' value="{{ $matter->id }}" name="matter_id">
  <table>
    <tr>
      <th>{{ __('Correspondent') }}</th>
      <th class="text-center">{{ __('To') }}</th>
      <th class="text-center">{{ __('CC') }}</th>
    </tr>
    @foreach($contacts as $contact)
    <tr>
      <td>
        {{ $contact->display_name ?? $contact->name }} {{ $contact->first_name }}
      </td>
      <td class="px-2">
        <input name="sendto-{{ $contact->actor_id }}" type="checkbox">
      </td>
      <td class="px-2">
        <input name="ccto-{{ $contact->actor_id }}" type="checkbox">
      </td>
    </tr>
  @endforeach
  </table>
  <table class="table table-sm" data-resource="/document/select/{{ $matter->id }}">
    <thead>
      <tr class="row table-primary">
        <th class="col-5">
          <input class="form-control form-control-sm filter" name="Summary"
            value="{{ array_key_exists('Summary', $oldfilters) ? $oldfilters['Summary'] : "" }}"
            placeholder="{{ __('Summary') }}">
        </th>
        <th class="col-2">
          <input class="form-control form-control-sm filter" name="Language"
            value="{{ array_key_exists('Language', $oldfilters) ? $oldfilters['Language'] : "" }}"
            placeholder="{{ __('Language') }}">
        </th>
        <th class="col-2">
          <input class="form-control form-control-sm filter" name="Category"
            value="{{ array_key_exists('Category', $oldfilters) ? $oldfilters['Category'] : "" }}"
            placeholder="{{ __('Category') }}">
        </th>
        <th class="col-2">
          <input class="form-control form-control-sm filter"
            title="{{ __($tableComments['style']) }}" name="Style"
            value="{{ array_key_exists('Style', $oldfilters) ? $oldfilters['Style'] : "" }}"
            placeholder="{{ __('Style') }}">
        </th>
        <th class="col-1 text-center">
          Send
        </th>
      </tr>
    </thead>
    <tbody id="tableList">
      @foreach($members as $member)
        <tr class="row" data-resource="/document/mailto/{{ $member->id }}">
          <td class="col-5">
            {{ $member->summary }}
          </td>
          <td class="col-2">
            {{ $member->language }}
          </td>
          <td class="col-2">
            {{ $member->category }}
          </td>
          <td class="col-2">
            {{ $member->style }}
          </td>
          <td class="col-1 text-center">
            <a class="sendDocument text-primary" href="#">
              <svg width="16" height="16" fill="currentColor" style="pointer-events: none"><use xlink:href="#envelope-fill"/></svg>
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</form>
