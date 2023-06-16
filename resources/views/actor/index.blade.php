@extends('layouts.app')

@section('content')
<legend class="text-primary">
    {{ _i('Actors') }}
    <a href="actor/create" class="btn btn-primary float-right" data-toggle="modal" data-target="#ajaxModal" title="{{ _i('Create Actor') }}">{{ _i("Create actor") }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary">
      <table class="table table-striped table-hover table-sm col">
        <thead>
          <tr id="filterFields" class="bg-primary text-light">
            <th class="border-top-0"><input class="form-control form-control-sm" name="Name" placeholder="{{ _i('Name') }}" value="{{ Request::get('Name') }}"></th>
            <th class="align-middle border-top-0">{{ _i("First name") }}</th>
            <th class="align-middle border-top-0">{{ _i("Display name") }}</th>
            <th class="align-middle text-center border-top-0">{{ _i('Company') }} <span class="float-right">{{ _i("Person") }}</span></th>
            <th class="border-top-0">
              <select id="person" class="custom-select custom-select-sm px-0" name="selector">
                <option value="" selected>{{ _i("All") }}</option>
                <option value="phy_p">{{ _i("Physical") }}</option>
                <option value="leg_p">{{ _i("Legal") }}</option>
                <option value="warn">{{ _i("Warn") }}</option>
              </select>
            </th>
          </tr>
        </thead>
        <tbody id="actorList">
          @foreach ($actorslist as $actor)
          <tr class="reveal-hidden" data-id="{{ $actor->id }}">
            <td>
              <a @if($actor->warn) class="text-danger text-decoration-none" @endif href="/actor/{{ $actor->id }}" data-panel="ajaxPanel" title="{{ _i('Actor data') }}">
                {{ $actor->name }}
              </a>
            </td>
            <td>{{ $actor->first_name }}</td>
            <td>{{ $actor->display_name }}</td>
            <td>{{ empty($actor->company) ? '' : $actor->company->name }}</td>
            <td>
              @if ($actor->phy_person)
              {{ _i("Physical") }}
              @else
              {{ _i("Legal") }}
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
        {{ _i("Actor information") }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ _i("Click on actor name to view and edit details") }}
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
@endsection
