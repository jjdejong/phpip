<div class="card border-info shadow" style="position: absolute; left: 150px; z-index: 1000; width: 220px">
    <div class="card-header bg-info text-white p-1">Add {{ $role_name }}</div>
    <form class="card-body text-body p-1" id="{{ $role_name }}" wire:submit.prevent="submit">
        @if ($role_name == 'Actor')
            @livewire('role-autocomplete', ['placeholder' => 'Role', 'inputClass' => 'form-control form-control-sm'])
        @endif
        @livewire('actor-autocomplete', ['placeholder' => 'Name', 'inputClass' => 'form-control form-control-sm'])
        <input type="text" wire:model.defer="actorPivot.actor_ref" class="form-control form-control-sm" placeholder="Reference">
        <div class="form-group">
            <div class="form-check my-1">
                <input class="form-check-input mt-0" type="radio" id="actorShared"
                    wire:model="actorPivot.shared"
                    value="1">
                <label class="form-check-label" for="actorShared">Add to container and share</label>
            </div>
            <div class="form-check my-1">
                <input class="form-check-input mt-0" type="radio" id="actorNotShared"
                    wire:model="actorPivot.shared"
                    value="0">
                <label class="form-check-label" for="actorNotShared">Add to this matter only (not shared)</label>
            </div>
        </div>
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-info btn-sm">&check;</button>
            <button type="button" class="btn btn-outline-info btn-sm" wire:click.stop="$emitUp('refreshActorCard', 'closeActorAdd')">&times;</button>
        </div>
        @error('actorPivot.role', 'actorPivot.actor_id')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    </form>
</div>
