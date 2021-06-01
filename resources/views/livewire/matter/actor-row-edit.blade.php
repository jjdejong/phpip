<tr class="reveal-hidden" data-n="{{ $actor_item->display_order }}">
    <td draggable="true" class="bg-light">&equiv;</td>
    <td><input type="text" class="form-control noformat" name="actor_id" data-ac="/actor/autocomplete" placeholder="{{ $actor_item->display_name }}"></td>
    <td><input type="text" class="form-control form-control-sm form-control-plaintext" wire:model.lazy="actorPivot.actor_ref"></td>
    <td><input type="text" class="form-control noformat" name="company_id" data-ac="/actor/autocomplete" value="{{ $actor_item->company }}"></td>
    <td><input type="date" class="form-control form-control-sm form-control-plaintext" wire:model.lazy="actorPivot.date"></td>
    <td><input type="text" class="form-control form-control-sm form-control-plaintext" size="6" wire:model.lazy="actorPivot.rate"></td>
    <td><input type="checkbox" class="form-control form-control-sm" wire:model="actorPivot.shared"></td>
    <td><input type="text" class="form-control form-control-sm form-control-plaintext" size="2" wire:model.lazy="actorPivot.display_order"></td>
    <td><input type="text" class="form-control noformat" data-ac="/role/autocomplete" name="role" placeholder="Change"></td>
    <td><a href="#" class="hidden-action text-danger" wire:click.prevent="removeActor" title="Remove actor">&CircleMinus;</a></td>
</tr>
