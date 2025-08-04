@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    {{ __('Actors') }}
    <a href="actor/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Add Actor') }}">{{ __('Create actor') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th>
              <div class="input-group input-group-sm" style="width: 150px;">
                <input class="form-control" name="Name" placeholder="{{ __('Name') }}" value="{{ Request::get('Name') }}">
                <button class="btn btn-outline-secondary clear-filter" type="button" style="display: none;" data-target="Name">
                  <span>&times;</span>
                </button>
              </div>
            </th>
            <th>{{ __('First name') }}</th>
            <th>{{ __('Display name') }}</th>
            <th class="text-center">{{ __('Company') }} <span class="float-end">{{ __('Person') }}</span></th>
            <th>
              <select id="person" class="form-select form-select-sm px-0" name="selector">
                <option value="" selected>{{ __('All') }}</option>
                <option value="phy_p">{{ __('Physical') }}</option>
                <option value="leg_p">{{ __('Legal') }}</option>
                <option value="warn">{{ __('Warn') }}</option>
              </select>
            </th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($actorslist as $actor)
          <tr class="reveal-hidden" data-id="{{ $actor->id }}">
            <td>
              <a @if($actor->warn) class="text-danger text-decoration-none" @endif href="/actor/{{ $actor->id }}" data-panel="ajaxPanel" title="{{ __('Actor data') }}">
                {{ $actor->name }}
              </a>
            </td>
            <td>{{ $actor->first_name }}</td>
            <td>{{ $actor->display_name }}</td>
            <td nowrap>{{ empty($actor->company) ? '' : $actor->company->name }}</td>
            <td>
              @if ($actor->phy_person)
              {{ __('Physical') }}
              @else
              {{ __('Legal') }}
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
        {{ __('Actor information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on actor name to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
<script>
  person.onchange = (e) => {
    if (e.target.value.length === 0) {
      url.searchParams.delete(e.target.name);
    } else {
      url.searchParams.set(e.target.name, e.target.value);
    }
    refreshList();
  }
</script>
@endsection
