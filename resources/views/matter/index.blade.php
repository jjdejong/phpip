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

@section('script')
<script type="text/javascript">

  var url = new URL(window.location.href);

  function refreshMatterList() {
    url.searchParams.delete('page');
    window.history.pushState('', 'phpIP', url);
    reloadPart(url, 'matterList');
  }

  filterFields.onclick = e => {
    if (e.target.matches('.sortable')) {
      for (elt of filterFields.getElementsByClassName('sortable')) {
        elt.classList.remove('active');
        elt.innerHTML = '&UpDownArrow;';
      }
      e.target.classList.add('active');
      url.searchParams.set('sortkey', e.target.dataset.sortkey);
      url.searchParams.set('sortdir', e.target.dataset.sortdir);
      if (e.target.dataset.sortdir === 'asc') {
        e.target.dataset.sortdir = 'desc';
        e.target.innerHTML = '&uarr;';
      } else {
        e.target.dataset.sortdir = 'asc';
        e.target.innerHTML = '&darr;';
      }
      refreshMatterList();
    }
  }

  filterButtons.onclick = e => {
    switch (e.target.id) {
      case 'showStatus':
        for (td of document.getElementsByClassName('tab1')) {
          td.classList.remove('d-none');
        }
        for (td of document.getElementsByClassName('tab0')) {
          td.classList.add('d-none');
        }
        url.searchParams.set('tab', '1');
        window.history.pushState('', 'phpIP', url);
        break;
      case 'showActors':
        for (td of document.getElementsByClassName('tab0')) {
          td.classList.remove('d-none');
        }
        for (td of document.getElementsByClassName('tab1')) {
          td.classList.add('d-none');
        }
        url.searchParams.set('tab', '0');
        window.history.pushState('', 'phpIP', url);
        break;
      case 'showContainers':
        if (url.searchParams.has('Ctnr')) {
          url.searchParams.delete('Ctnr');
        } else {
          url.searchParams.set('Ctnr', '1');
        }
        refreshMatterList();
        break;
      case 'showResponsible':
        if (url.searchParams.has('responsible')) {
          url.searchParams.delete('responsible');
        } else {
          url.searchParams.set('responsible', e.target.dataset.responsible);
        }
        refreshMatterList();
        break;
      case 'includeDead':
        if (url.searchParams.has('include_dead')) {
          url.searchParams.delete('include_dead');
        } else {
          url.searchParams.set('include_dead', '1');
        }
        refreshMatterList();
        break;
    }
  }

  exportList.onclick = e => {
    let exportUrl = '/matter/export' + url.search;
    e.preventDefault(); //stop the browser from following
    window.location.href = exportUrl;
  };
  report.onclick = e => {
    var l = document.getElementById("report_list");
    var report = l.options[l.selectedIndex].value;
    url.searchParams.set("report_list", report)
    let reportUrl = '/matter/report' + url.search;
    e.preventDefault(); //stop the browser from following
    window.location.href = reportUrl;
  };

  filterFields.addEventListener('input', debounce( e => {
    if (e.target.value.length === 0) {
      url.searchParams.delete(e.target.name);
    } else {
      url.searchParams.set(e.target.name, e.target.value);
    }
    refreshMatterList();
  }, 500));

  clearFilters.onclick = () => {
    window.location.href = '/matter';
  };

</script>
@endsection

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
      <div class="btn-group-toggle mr-3" data-toggle="buttons">
        <label id="showContainers" class="btn btn-info {{ Request::get('Ctnr') ? 'active' : '' }}">
          <input type="checkbox" name="Ctnr" {{ Request::get('Ctnr') ? 'checked' : '' }}> Show Containers
        </label>
      </div>
      <div class="btn-group btn-group-toggle mr-3" data-toggle="buttons" id="actorStatus">
        <label id="showActors" class="btn btn-info {{ $tab == 1 ? '' : 'active' }}">
          <input type="radio" name="tab" {{ $tab == 1 ? '' : 'checked' }}> Actor View
        </label>
        <label id="showStatus" class="btn btn-info {{ $tab == 1 ? 'active' : '' }}">
          <input type="radio" name="tab" {{ $tab == 1 ? 'checked' : '' }}> Status View
        </label>
      </div>
      <div class="btn-group-toggle mr-3" id="mineAll" data-toggle="buttons">
        <label id="showResponsible" class="btn btn-info {{ Request::has('responsible') ? 'active' : '' }}" data-responsible="{{ Auth::user()->login }}">
          <input type="checkbox" name="responsible" {{ Request::has('responsible') ? 'checked' : '' }}> Show Mine
        </label>
      </div>
      <div class="btn-group-toggle mr-3" data-toggle="buttons">
        <label id="includeDead" class="btn btn-info {{ Request::get('include_dead') ? 'active' : '' }}">
          <input type="checkbox" name="include_dead" {{ Request::get('include_dead') ? 'checked' : '' }}> Include Dead
        </label>
      </div>
      <input type="hidden" name="display_with" value="{{ Request::get('display_with') }}">
      <div class="btn-group mr-3">
        <button id="exportList" type="button" class="btn btn-secondary"> &DownArrowBar; Export</button>
      </div>
      <form method="POST" action="/matter/report">
        <div class="btn-group mr-3">
                <select class="custom-select" id="report_list" name="report_list">
                    <option value="report1-fr">By family, only alive (fr)</option>
                    <option value="report2-fr">By family, with dead (fr)</option>
                    <option value="report1" selected>By family, with dead (en)</option>
                </select>
        </div>
        <div class="btn-group mr-2">
            <button id="report" type="button" class="btn btn-secondary"> &DownArrowBar; Report</button>
        </div>
      </form>
      <div class="button-group">
        <button id="clearFilters" type="button" class="btn btn-dark">&larrpl; Clear filters</button>
      </div>
    </form>
  </div>
  <div class="card-body p-0">
    <table class="table table-striped table-hover table-sm mb-1">
      <thead>
        <tr id="filterFields">
          <td>
            <div class="input-group input-group-sm">
              <input class="form-control" name="Ref" placeholder="Ref" value="{{ Request::get('Ref') }}">
              <div class="input-group-append">
              <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'caseref' ? 'active' : '' }}" type="button" data-sortkey="caseref" data-sortdir="desc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td><input class="form-control form-control-sm px-0" size="3" name="Cat" placeholder="Cat" value="{{ Request::get('Cat') }}"></td>
          <td>
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Status" placeholder="Status" value="{{ Request::get('Status') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'event_name.name' ? 'active' : '' }}" type="button" data-sortkey="event_name.name" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          @cannot('client')
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Client" placeholder="Client" value="{{ Request::get('Client') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'cli.name' ? 'active' : '' }}" type="button" data-sortkey="cli.name" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          @endcannot
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="8" name="ClRef" placeholder="Cl. Ref" value="{{ Request::get('ClRef') }}"></td>
          @can('client')
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Applicant" placeholder="Applicant" value="{{ Request::get('Applicant') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'app.name' ? 'active' : '' }}" type="button" data-sortkey="app.name" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          @endcan
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Agent" placeholder="Agent" value="{{ Request::get('Agent') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'agt.name' ? 'active' : '' }}" type="button" data-sortkey="agt.name" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="16" name="AgtRef" placeholder="Agt. Ref" value="{{ Request::get('AgtRef') }}"></td>
          <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Title" placeholder="Title" value="{{ Request::get('Title') }}"></td>
          <td class="tab0 {{ $hideTab0 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Inventor1" placeholder="Inventor" value="{{ Request::get('Inventor1') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'inv.name' ? 'active' : '' }}" type="button" data-sortkey="inv.name" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Status_date" placeholder="Date" value="{{ Request::get('Status_date') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'status.event_date' ? 'active' : '' }}" type="button" data-sortkey="status.event_date" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Filed" placeholder="Filed" value="{{ Request::get('Filed') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'fil.event_date' ? 'active' : '' }}" type="button" data-sortkey="fil.event_date" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="FilNo" placeholder="Number" value="{{ Request::get('FilNo') }}"></td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Published" placeholder="Published" value="{{ Request::get('Published') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'pub.event_date' ? 'active' : '' }}" type="button" data-sortkey="pub.event_date" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="PubNo" placeholder="Number" value="{{ Request::get('PubNo') }}"></td>
          <td class="tab1 {{ $hideTab1 }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-sm" name="Granted" placeholder="Granted" value="{{ Request::get('Granted') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary sortable {{ Request::get('sortkey') == 'grt.event_date' ? 'active' : '' }}" type="button" data-sortkey="grt.event_date" data-sortdir="asc">&UpDownArrow;</button>
              </div>
            </div>
          </td>
          <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="GrtNo" placeholder="Number" value="{{ Request::get('GrtNo') }}"></td>
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
          @cannot('client')
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Client }}</td>
          @endcannot
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->ClRef }}</td>
          @can('client')
          <td class="tab0 {{ $hideTab0 }}">{{ $matter->Applicant }}</td>
          @endcan
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
