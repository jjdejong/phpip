<div id="actorPanel" class="card col-3 border-secondary p-0">
    <div class="card-header text-white bg-secondary p-1">
        Actors
        @canany(['admin', 'readwrite'])
        <a wire:click.prevent="$toggle('addActive')" class="badge badge-pill badge-light float-right" 
            title="Add Actor" href="#">
            &plus;
        </a>
        @if ($addActive)
            @livewire('matter.actor-add', [
                'matter_id' => $matter_id, 
                'container_id' => $container_id,
            ])
        @endif
        @endcanany
    </div>
    <div class="card-body bg-light p-1">
        @foreach ($actors as $role_name => $role_group)
            @livewire('matter.actor-card', [
                'matter_id' => $matter_id,
                'container_id' => $container_id,
                'role_group' => $role_group,
                'role_code' => $role_group->first()->role_code,
                'role_name' => $role_name,
            ], key($role_name))
        @endforeach
    </div>
</div>
