<tr class="reveal-hidden">
    <td>
        <input type="text" class="form-control-plaintext p-1" size="2" wire:model.lazy="actorPivot.display_order">
    </td>
    <td>
        @livewire('actor-autocomplete', ['placeholder' => $actor_item->name, 'inputClass' => 'form-control-plaintext p-1'])
    </td>
    <td>
        <input type="text" class="form-control-plaintext p-1" wire:model.lazy="actorPivot.actor_ref">
    </td>
    <td>
        @livewire('company-autocomplete', ['search' => $actor_item->company, 'inputClass' => 'form-control-plaintext p-1'])
    </td>
    <td>
        <input type="date" class="form-control-plaintext p-1" wire:model.lazy="actorPivot.date">
    </td>
    <td>
        <input type="text" class="form-control-plaintext p-1" size="6" wire:model.lazy="actorPivot.rate">
    </td>
    <td class="p-2">
        <input type="checkbox" wire:model="actorPivot.shared">
    </td>
    <td>
        @livewire('role-autocomplete', ['placeholder' => 'Change', 'inputClass' => 'form-control-plaintext p-1'])
    </td>
    <td class="align-middle">
        <a href="#" class="text-danger hidden-action" wire:click.prevent="removeActor" title="Remove actor">
            <i class="bi-trash"></i>
        </a>
    </td>
</tr>
