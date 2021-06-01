<span x-data="{open: false}" 
  id="edit-{{ $role_group->first()->role_name }}"
  style="position: relative">
  <a @click.prevent="open = !open" class="hidden-action float-right text-light font-weight-bold"
      title="Edit actors in {{ $role_group->first()->role_name }} group">
      &#9998;
  </a>
  <div class="card shadow border-primary p-2" style="position: absolute; left: 100px; z-index: 1000; width: 60rem"
    x-show.transition="open"
    @click.away="open = false">
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
        @foreach ( $role_group as $actor_item )
        @livewire('matter.actor-row-edit', ['actor_item' => $actor_item], key($actor_item->actor_id))
        @endforeach
      </tbody>
    </table>
  </div>
</span>