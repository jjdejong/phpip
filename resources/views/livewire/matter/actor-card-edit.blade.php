<div class="card border-primary shadow-lg" style="position: absolute; left: 100px; z-index: 1000; width: 60rem">
  <div class="card-header bg-info lead">
    Edit actors in {{ $role_name }} group
    <button type="button" class="close" wire:click.stop="$emitUp('actorChanged', 'closeActorEdit')">&times;</button>
  </div>
  <div class="card-body">
    <table class="table table-hover table-sm">
      <thead class="thead-light">
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">Name</th>
          <th class="text-center">Reference</th>
          <th class="text-center">Company</th>
          <th class="text-center">Date</th>
          <th class="text-center">Rate</th>
          <th class="text-center">Shared</th>
          <th class="text-center">Role</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($role_group as $actor_item)
          {{-- The random key value causes the child component to automatically refresh when the parent refreshes --}}
          @livewire('matter.actor-row-edit', [
            'actor_item' => $actor_item,
            'container_id' => $container_id
          ], key(Str::random()))
        @endforeach
      </tbody>
    </table>
  </div>
</div>