<div class="card border-primary shadow" style="position: absolute; left: 100px; z-index: 1000; width: 60rem">
  <div class="card-header bg-info lead">
    Edit actors in {{ $role_group->first()->role_name }} group
    <button type="button" class="close" wire:click.stop="$emitUp('refreshActorCard', 'closeActorEdit')">&times;</button>
  </div>
  <div class="card-body">
    <table class="table table-hover table-sm">
      <thead class="thead-light">
        <tr>
          <th></th>
          <th>Name</th>
          <th>Reference</th>
          <th>Company</th>
          <th>Date</th>
          <th>Rate</th>
          <th>Shared</th>
          <th>N</th>
          <th>Role</th>
          <th style="width: 24px;">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($role_group as $actor_item)
          @livewire('matter.actor-row-edit', ['actor_item' => $actor_item], key($actor_item->id))
        @endforeach
      </tbody>
    </table>
  </div>
</div>