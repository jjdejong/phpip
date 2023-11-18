<table class="table table-hover table-sm">
  <thead class="table-light">
    <tr>
      <th></th>
      <th>{{ _i("Name") }}</th>
      <th>{{ _i("Reference") }}</th>
      <th>{{ _i("Company") }}</th>
      <th>{{ _i("Date") }}</th>
      <th>{{ _i("Rate") }}</th>
      <th>{{ _i("Shared") }}</th>
      <th>N</th>
      <th>{{ _i("Role") }}</th>
      <th style="width: 24px;">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    @foreach ( $role_group as $actor_item )
    <tr class="reveal-hidden" data-resource="/actor-pivot/{{ $actor_item->id }}" data-n="{{ $actor_item->display_order }}">
      <td draggable="true" class="bg-light">&equiv;</td>
      <td><input type="text" class="form-control noformat" name="actor_id" data-ac="/actor/autocomplete" placeholder="{{ $actor_item->display_name }}"></td>
      <td><input type="text" class="form-control noformat" name="actor_ref" value="{{ $actor_item->actor_ref }}"></td>
      <td><input type="text" class="form-control noformat" name="company_id" data-ac="/actor/autocomplete" value="{{ $actor_item->company }}"></td>
      <td><input type="date" class="form-control noformat" name="date" value="{{ $actor_item->date }}"></td>
      <td><input type="text" class="form-control noformat" size="6" name="rate" value="{{ $actor_item->rate }}"></td>
      <td><input type="checkbox" class="form-control noformat" name="shared" {{ $actor_item->shared ? 'checked' : '' }}></td>
      <td><input type="text" class="form-control noformat" size="2" name="display_order" value="{{ $actor_item->display_order }}"></td>
      <td><input type="text" class="form-control noformat" data-ac="/role/autocomplete" name="role" placeholder="{{ _i('Change') }}"></td>
      <td><a href="javascript:void(0);" class="hidden-action text-danger" id="removeActor" title="{{ _i('Remove actor') }}">&CircleMinus;</a></td>
    </tr>
    @endforeach
  </tbody>
</table>
