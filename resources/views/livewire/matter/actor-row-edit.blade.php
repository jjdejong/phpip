<tr data-n="{{ $actor_item->display_order }}">
    <td draggable="true" class="bg-light">&equiv;</td>
    <td>@livewire('actor-autocomplete', ['placeholder' => $actor_item->name, 'inputClass' => 'form-control-sm form-control-plaintext'])</td>
    <td><input type="text" class="form-control-sm form-control-plaintext" wire:model.lazy="actorPivot.actor_ref"></td>
    <td>@livewire('company-autocomplete', ['placeholder' => $actor_item->company, 'inputClass' => 'form-control-sm form-control-plaintext'])</td>
    <td><input type="date" class="form-control-sm form-control-plaintext" wire:model.lazy="actorPivot.date"></td>
    <td><input type="text" class="form-control-sm form-control-plaintext" size="6" wire:model.lazy="actorPivot.rate"></td>
    <td><input type="checkbox" wire:model="actorPivot.shared"></td>
    <td><input type="text" class="form-control-sm form-control-plaintext" size="2" wire:model.lazy="actorPivot.display_order"></td>
    <td>@livewire('role-autocomplete', ['placeholder' => 'Change', 'inputClass' => 'form-control-sm form-control-plaintext'])</td>
    <td><a href="#" class="text-danger" wire:click.prevent="removeActor" title="Remove actor">&CircleMinus;</a></td>
</tr>
