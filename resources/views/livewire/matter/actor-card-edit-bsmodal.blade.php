<span>
  <a class="hidden-action float-right text-light font-weight-bold"
    data-toggle="modal" data-target="#{{ $role_group->first()->role_code }}-Edit"
    title="Edit actors in {{ $role_group->first()->role_name }} group">
    &#9998;
  </a>
  <div class="modal fade" id="{{ $role_group->first()->role_code }}-Edit" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit actors in {{ $role_group->first()->role_name }} group</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
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
        <div class="modal-footer">
          <span id="footerAlert" class="alert float-left"></span>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</span>