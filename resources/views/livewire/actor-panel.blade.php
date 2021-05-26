<div id="actorPanel" class="card col-3 border-secondary p-0">
    <div class="card-header reveal-hidden text-white bg-secondary p-1">
        Actors
        @canany(['admin', 'readwrite'])
        @livewire('add-actor', [
            'matter_id' => $matter_id, 
            'container_id' => $container_id,
        ])
        @endcanany
    </div>
    <div class="card-body bg-light p-1">
        @foreach ($actors as $role_name => $role_group)
            @livewire('actor-card', [
                'matter_id' => $matter_id, 
                'container_id' => $container_id,
                'role_name' => $role_name,
                'role_group' => $role_group,
            ], key($role_name))
        @endforeach
    </div>
</div>
