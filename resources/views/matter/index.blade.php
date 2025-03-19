@php
  if (Request::get('tab') == 1) {
    $hideTab0 = 'd-none';
    $hideTab1 = '';
    $tab = 1;
  } else {
    $hideTab0 = '';
    $hideTab1 = 'd-none';
    $tab = 0;
  }
@endphp

@extends('layouts.app')

@section('style')
<style>
  input:not(:placeholder-shown) {
    border-color: rgb(0, 190, 190);
    font-weight: bold;
  }
</style>
@endsection

@section('content')
<div class="card border-primary mb-0">
  <div id="filterButtons" class="card-header bg-primary p-1">
    <form class="btn-toolbar" role="toolbar">
      <div class="btn-group me-3">
        <input type="checkbox" class="btn-check" name="Ctnr" {{ Request::get('Ctnr') ? 'checked' : '' }} id="btnshowctnr">
        <label id="showContainers" class="btn btn-outline-info" for="btnshowctnr">{{ __('Show Containers') }}</label>
      </div>
      <div class="btn-group me-3" id="actorStatus">
        <input type="radio" class="btn-check" name="tab" {{ $tab == 1 ? '' : 'checked' }} id="btnactorview">
        <label id="showActors" class="btn btn-outline-info" for="btnactorview">{{ __('Actor View') }}</label>
        <input type="radio" class="btn-check" name="tab" {{ $tab == 1 ? 'checked' : '' }} id="btnstatusview">
        <label id="showStatus" class="btn btn-outline-info" for="btnstatusview">{{ __('Status View') }}</label>
      </div>
      @can('readonly')
      <div class="btn-group me-3" id="mineAll">
        <input type="checkbox" class="btn-check" name="responsible" {{ Request::has('responsible') ? 'checked' : '' }} id="btnshowmine">
        <label id="showResponsible" class="btn btn-outline-info" data-responsible="{{ Auth::user()->login }}" for="btnshowmine">{{ __('Show Mine') }}</label>
      </div>
      @endcan
      <div class="btn-group me-3">
        <input type="checkbox" class="btn-check" name="include_dead" {{ Request::get('include_dead') ? 'checked' : '' }} id="btnincludedead">
        <label id="includeDead" class="btn btn-outline-info" for="btnincludedead">{{ __('Include Dead') }}</label>
      </div>
      <input type="hidden" name="display_with" value="{{ Request::get('display_with') }}">
      <div class="btn-group me-3">
        <button id="exportList" type="button" class="btn btn-secondary"> &DownArrowBar; {{ __('Export') }}</button>
      </div>
      <div class="button-group">
        <button id="clearFilters" type="button" class="btn btn-dark" onclick="window.location.href = '/matter'">&larrpl; {{ __('Clear filters') }}</button>
      </div>
    </form>
  </div>
  <div class="card-body p-0">
    <table class="table table-striped table-hover table-sm mb-1">
      <thead>
        <tr id="filterFields">
          <td>
            <div class="input-group input-group-sm">
              <input class="form-control" name="Ref" placeholder="{{ __('Ref') }}" value="{{ Request::get('Ref') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'caseref' ? 'active' : '' }}" type="button" data-sortkey="caseref" data-sortdir="desc">&UpDownArrow;</button>
            </div>
          </td>
          <td><input class="form-control form-control-sm px-0" size="3" name="Cat" placeholder="{{ __('Cat') }}" value="{{ Request::get('Cat') }}"></td>
          <td>
            <div class="input-group input-group-sm">
              <input class="form-control" name="Status" placeholder="{{ __('Status') }}" value="{{ Request::get('Status') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'event_name.name' ? 'active' : '' }}" type="button" data-sortkey="event_name.name" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          @can('readonly')
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Client" placeholder="{{ __('Client') }}" value="{{ Request::get('Client') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'cli.name' ? 'active' : '' }}" type="button" data-sortkey="cli.name" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          @endcan
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="8" name="ClRef" placeholder="{{ __('Cl. Ref') }}" value="{{ Request::get('ClRef') }}"></td>
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Applicant" placeholder="{{ __('Applicant') }}" value="{{ Request::get('Applicant') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'app.name' ? 'active' : '' }}" type="button" data-sortkey="app.name" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Agent" placeholder="{{ __('Agent') }}" value="{{ Request::get('Agent') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'agt.name' ? 'active' : '' }}" type="button" data-sortkey="agt.name" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="16" name="AgtRef" placeholder="{{ __('Agt. Ref') }}" value="{{ Request::get('AgtRef') }}"></td>
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Title" placeholder="{{ __('Title') }}" value="{{ Request::get('Title') }}"></td>
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Inventor1" placeholder="{{ __('Inventor') }}" value="{{ Request::get('Inventor1') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'inv.name' ? 'active' : '' }}" type="button" data-sortkey="inv.name" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Status_date" placeholder="{{ __('Date') }}" value="{{ Request::get('Status_date') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'status.event_date' ? 'active' : '' }}" type="button" data-sortkey="status.event_date" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Filed" placeholder="{{ __('Filed') }}" value="{{ Request::get('Filed') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'fil.event_date' ? 'active' : '' }}" type="button" data-sortkey="fil.event_date" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="FilNo" placeholder="{{ __('Number') }}" value="{{ Request::get('FilNo') }}"></td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Published" placeholder="{{ __('Published') }}" value="{{ Request::get('Published') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'pub.event_date' ? 'active' : '' }}" type="button" data-sortkey="pub.event_date" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="PubNo" placeholder="{{ __('Number') }}" value="{{ Request::get('PubNo') }}"></td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control" name="Granted" placeholder="{{ __('Granted/Reg\'d') }}" value="{{ Request::get('Granted') }}">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'grt.event_date' ? 'active' : '' }}" type="button" data-sortkey="grt.event_date" data-sortdir="asc">&UpDownArrow;</button>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="GrtNo" placeholder="{{ __('Number') }}" value="{{ Request::get('GrtNo') }}"></td>
        </tr>
      </thead>
      <tbody id="matterList">
        @foreach ($matters as $matter)
        @php // Format the publication number for searching on Espacenet
        $published = 0;
        if ( $matter->PubNo || $matter->GrtNo) {
          $published = 1;
          if ( $matter->origin == 'EP' )
            $CC = 'EP';
          else
            $CC = $matter->country;
          $removethese = [ "/^$matter->country/", '/ /', '/,/', '/-/', '/\//' ];
          $pubno = preg_replace ( $removethese, '', $matter->PubNo );
          if ( $CC == 'US' ) {
            if ( $matter->GrtNo )
              $pubno = preg_replace ( $removethese, '', $matter->GrtNo );
            else
              $pubno = substr ( $pubno, 0, 4 ) . substr ( $pubno, - 6 );
          }
        }
        @endphp
        @if ( $matter->container_id )
        <tr>
          @else
        <tr class="table-info">
          @endif
          <td {!! $matter->dead ? 'style="text-decoration: line-through;"' : '' !!}><a href="/matter/{{ $matter->id }}" target="_blank">{{ $matter->Ref }}</a></td>
          <td>{{ $matter->Cat }}</td>
          <td>
            @if ( $published )
            <a href="http://worldwide.espacenet.com/publicationDetails/biblio?DB=EPODOC&CC={{ $CC }}&NR={{ $pubno }}" target="_blank" title="Open in Espacenet">{{ $matter->Status }}</a>
            @else
            {{ $matter->Status }}
            @endif
          </td>
          @can('readonly')
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Client }}</td>
          @endcan
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->ClRef }}</td>
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Applicant }}</td>
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Agent }}</td>
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->AgtRef }}</td>
          @if ( $matter->container_id && $matter->Title2 )
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Title2 }}</td>
          @else
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Title }}</td>
          @endif
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Inventor1 }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->Status_date }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->Filed }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->FilNo }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->Published }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->PubNo }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->Granted }}</td>
          <td class="tab1 {{ $hideTab1 }}">{{ $matter->GrtNo }}</td>
        </tr>
        @endforeach
        <tr>
          <td colspan="9">{{ $matters->links() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/matter-index.js') }}" defer></script>
@endsection
