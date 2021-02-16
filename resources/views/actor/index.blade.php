@extends('layouts.app')

@section('content')
<legend class="text-primary">
    Actors
    <a href="actor/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="Create Actor">Create actor</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary">
      <table class="table table-striped table-hover table-sm col">
        <thead>
          <tr id="filterFields" class="bg-primary text-light">
            <th class="border-top-0"><input class="form-control form-control-sm" name="Name" placeholder="Name" value="{{ Request::get('Name') }}"></th>
            <th class="align-middle border-top-0">First name</th>
            <th class="align-middle border-top-0">Display name</th>
            <th class="align-middle text-center border-top-0">Company <span class="float-right">Person</span></th>
            <th class="border-top-0">
              <select id="person" class="custom-select custom-select-sm px-0" name="selector">
                <option value="" selected>All</option>
                <option value="phy_p">Physical</option>
                <option value="leg_p">Legal</option>
                <option value="warn">Warn</option>
              </select>
            </th>
          </tr>
        </thead>
        <tbody id="actorList">
          @foreach ($actorslist as $actor)
          <tr class="reveal-hidden" data-id="{{ $actor->id }}">
            <td>
              <a @if($actor->warn) class="text-danger text-decoration-none" @endif href="/actor/{{ $actor->id }}" data-panel="ajaxPanel" title="Actor data">
                {{ $actor->name }}
              </a>
            </td>
            <td>{{ $actor->first_name }}</td>
            <td>{{ $actor->display_name }}</td>
            <td>{{ empty($actor->company) ? '' : $actor->company->name }}</td>
            <td>
              @if ($actor->phy_person)
              Physical
              @else
              Legal
              @endif
            </td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $actorslist->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        Actor information
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          Click on actor name to view and edit details
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>

  var url = new URL(window.location.href);

  function refreshActorList() {
    window.history.pushState('', 'phpIP', url)
    reloadPart(url, 'actorList');
  }

  person.onchange = (e) => {
    if (e.target.value.length === 0) {
      url.searchParams.delete(e.target.name);
    } else {
      url.searchParams.set(e.target.name, e.target.value);
    }
    refreshActorList();
  }

  filterFields.addEventListener('input', debounce( e => {
    if (e.target.value.length === 0) {
      url.searchParams.delete(e.target.name);
    } else {
      url.searchParams.set(e.target.name, e.target.value);
    }
    refreshActorList();
  }, 300));

</script>
@stop
