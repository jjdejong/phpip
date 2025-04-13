<table class="table table-hover table-sm">
  <thead class="table-light">
    <tr>
      <th></th>
      <th>{{ __('Name') }}</th>
      <th>{{ __('Reference') }}</th>
      <th>{{ __('Company') }}</th>
      <th>{{ __('Date') }}</th>
      <th>{{ __('Rate') }}</th>
      <th>{{ __('Shared') }}</th>
      <th>N</th>
      <th>{{ __('Role') }}</th>
      <th style="width: 24px;">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    @foreach ( $role_group as $actor_item )
    <tr class="reveal-hidden" data-resource="/actor-pivot/{{ $actor_item->id }}" data-n="{{ $actor_item->display_order }}">
      <td draggable="true" class="bg-light">&equiv;</td>
      <td><input type="text" class="form-control noformat {{ $actor_item->inherited ? 'fst-italic' : '' }}" name="actor_id" data-ac="/actor/autocomplete" placeholder="{{ $actor_item->display_name }}"></td>
      <td><input type="text" class="form-control noformat" name="actor_ref" value="{{ $actor_item->actor_ref }}"></td>
      <td><input type="text" class="form-control noformat" name="company_id" data-ac="/actor/autocomplete" value="{{ $actor_item->company }}"></td>
      <td><input type="date" class="form-control noformat" name="date" value="{{ $actor_item->date }}"></td>
      <td><input type="text" class="form-control noformat" size="6" name="rate" value="{{ $actor_item->rate }}"></td>
      <td><input type="checkbox" class="noformat" name="shared" {{ $actor_item->shared ? 'checked' : '' }}></td>
      <td><input type="text" class="form-control noformat" size="2" name="display_order" value="{{ $actor_item->display_order }}"></td>
      <td><input type="text" class="form-control noformat" data-ac="/role/autocomplete" name="role" placeholder="{{ __('Change') }}"></td>
      <td><a href="javascript:void(0);" class="hidden-action text-danger" id="removeActor" title="{{ __('Remove actor') }}">
        <svg width="14" height="14" fill="currentColor" style="pointer-events: none"><use xlink:href="#trash-fill"></use></svg>
      </a></td>
    </tr>
    @endforeach
  </tbody>
</table>
