@php
  if (Request::get('tab') == 1) {
    $hideTab0 = 'd-none';
    $hideTab1 = '';
  } else {
    $hideTab0 = '';
    $hideTab1 = 'd-none';
  }
@endphp

@extends('layouts.app')

@section('script')
<script type="text/javascript">

  var url = new URL(window.location.href);

  function refreshMatterList() {
    window.history.pushState('', 'phpIP', url);
    reloadPart(url, 'matterList');
  }

  sortHeaders.onclick = e => {
    if (e.target.matches('.sortable')) {
      url.searchParams.set('sortkey', e.target.dataset.sortkey);
      url.searchParams.set('sortdir', e.target.dataset.sortdir);
      if (e.target.dataset.sortdir === 'asc') {
        e.target.dataset.sortdir = 'desc';
      } else {
        e.target.dataset.sortdir = 'asc';
      }
      refreshMatterList();
    }
  }

  filterButtons.onchange = e => {
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
      case 'showAll':
        url.searchParams.delete('Ctnr');
        refreshMatterList();
        break;
      case 'showContainers':
        url.searchParams.set('Ctnr', '1');
        refreshMatterList();
        break;
      case 'showResponsible':
        if (url.searchParams.has('responsible')) {
          url.searchParams.delete('responsible');
        } else {
          url.searchParams.set('responsible', e.target.value);
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
@stop

@section('style')
<style>
  input:not(:placeholder-shown) {
    border-color: green;
  }
</style>
@stop

@section('content')
<div class="card mb-0">
  <div id="filterButtons" class="card-header">
    <form class="btn-toolbar" role="toolbar">
      <div class="btn-group btn-group-toggle mr-3" data-toggle="buttons" id="containerAll">
        <label class="btn btn-info {{ Request::filled('Ctnr') ? '' : 'active' }}">
          <input type="radio" id="showAll" name="Ctnr" value=""> Show All
        </label>
        <label class="btn btn-info {{ Request::filled('Ctnr') ? 'active' : '' }}">
          <input type="radio" id="showContainers" name="Ctnr" value="1"> Show Containers
        </label>
      </div>
      <div class="btn-group btn-group-toggle mr-3" data-toggle="buttons" id="actorStatus">
        <label class="btn btn-info {{ Request::get('tab') == 1 ? '' : 'active' }}">
          <input type="radio" id="showActors" name="tab" value="0"> Actor View
        </label>
        <label class="btn btn-info {{ Request::get('tab') == 1 ? 'active' : '' }}">
          <input type="radio" id="showStatus" name="tab" value="1"> Status View
        </label>
      </div>
      <div class="btn-group-toggle mr-3" id="mineAll" data-toggle="buttons">
        <label class="btn btn-info {{ Request::get('responsible') ? 'active' : '' }}">
          <input type="checkbox" id="showResponsible" name="responsible" value="{{ Auth::user ()->login }}"> Show Mine
        </label>
      </div>
      <input type="hidden" id="sortkey" name="sortkey" value="{{ Request::get('sortkey') }}">
      <input type="hidden" id="sortdir" name="sortdir" value="{{ Request::get('sortdir') }}">
      <input type="hidden" name="display_with" value="{{ Request::get('display_with') }}">
      <div class="btn-group mr-3">
        <button id="exportList" type="button" class="btn btn-primary"> &DownArrowBar; Export</button>
      </div>
      <div class="button-group">
        <button id="clearFilters" type="button" class="btn btn-primary">&larrpl; Clear filters</button>
      </div>
    </form>
  </div>
</div>
<table class="table table-striped table-hover table-sm">
  <thead>
    <tr id="sortHeaders" class="bg-light">
      <th><a href="#" class="sortable" data-sortkey="caseref" data-sortdir="desc">Reference</a></th>
      <th>Cat.</th>
      <th><a href="#" class="sortable" data-sortkey="event_name.name" data-sortdir="asc">Status</a></th>
      <th class="tab0 {{ $hideTab0 }}"><a href="#" class="sortable" data-sortkey="cli.name" data-sortdir="asc">Client</a></th>
      <th class="tab0 {{ $hideTab0 }}">Client&nbsp;Ref.</th>
      <th class="tab0 {{ $hideTab0 }}"><a href="#" class="sortable" data-sortkey="agt.name" data-sortdir="asc">Agent</a></th>
      <th class="tab0 {{ $hideTab0 }}">Agent&nbsp;Ref.</th>
      <th class="tab0 {{ $hideTab0 }}">Title/Detail</th>
      <th class="tab0 {{ $hideTab0 }}"><a href="#" class="sortable" data-sortkey="inv.name" data-sortdir="asc">Inventor</a></th>
      <th class="tab1 {{ $hideTab1 }}"><a href="#" class="sortable" data-sortkey="status.event_date" data-sortdir="asc">Date</a></th>
      <th class="tab1 {{ $hideTab1 }}"><a href="#" class="sortable" data-sortkey="fil.event_date" data-sortdir="asc">Filed</a></th>
      <th class="tab1 {{ $hideTab1 }}">Number</th>
      <th class="tab1 {{ $hideTab1 }}"><a href="#" class="sortable" data-sortkey="pub.event_date" data-sortdir="asc">Published</a></th>
      <th class="tab1 {{ $hideTab1 }}">Number</th>
      <th class="tab1 {{ $hideTab1 }}"><a href="#" class="sortable" data-sortkey="grt.event_date" data-sortdir="asc">Granted</a></th>
      <th class="tab1 {{ $hideTab1 }}">Number</th>
    </tr>
    <tr id="filterFields">
      <td><input class="form-control form-control-sm" name="Ref" placeholder="Ref" value="{{ Request::get('Ref') }}"></td>
      <td><input class="form-control form-control-sm" size="3" name="Cat" placeholder="Cat" value="{{ Request::get('Cat') }}"></td>
      <td><input class="form-control form-control-sm" name="Status" placeholder="Status" value="{{ Request::get('Status') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Client" placeholder="Client" value="{{ Request::get('Client') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="8" name="ClRef" placeholder="Cl. Ref" value="{{ Request::get('ClRef') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Agent" placeholder="Agent" value="{{ Request::get('Agent') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" size="16" name="AgtRef" placeholder="Agt. Ref" value="{{ Request::get('AgtRef') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Title" placeholder="Title" value="{{ Request::get('Title') }}"></td>
      <td class="tab0 {{ $hideTab0 }}"><input class="form-control form-control-sm" name="Inventor1" placeholder="Inventor" value="{{ Request::get('Inventor1') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="Status_date" placeholder="Date" value="{{ Request::get('Status_date') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="Filed" placeholder="Filed" value="{{ Request::get('Filed') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="FilNo" placeholder="Number" value="{{ Request::get('FilNo') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="Published" placeholder="Published" value="{{ Request::get('Published') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="PubNo" placeholder="Number" value="{{ Request::get('PubNo') }}"></td>
      <td class="tab1 {{ $hideTab1 }}"><input class="form-control form-control-sm" name="Granted" placeholder="Granted" value="{{ Request::get('Granted') }}"></td>
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
      <td class="tab0 {{ $hideTab0 }}">{{ $matter->Client }}</td>
      <td class="tab0 {{ $hideTab0 }}">{{ $matter->ClRef }}</td>
      <td class="tab0 {{ $hideTab0 }}">{{ $matter->Agent }}</td>
      <td class="tab0 {{ $hideTab0 }}">{{ $matter->AgtRef }}</td>
      @if ( $matter->container_id )
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
      <td colspan="9">
        {{ $matters->links() }}
      </td>
    </tr>
  </tbody>
</table>

@stop
