@php
$titles = $matter->titles->groupBy('type_name');
$classifiers = $matter->classifiers->groupBy('type_name');
$actors = $matter->actors->groupBy('role_name');
$linkedBy = $matter->linkedBy->groupBy('type_code');
@endphp

@extends('layouts.app')

@section('content')

<div class="row card-deck mb-1">
  <div id="refsPanel" class="card border-primary col-3 p-0">
    <div class="card-header bg-primary text-light reveal-hidden lead p-1"  {!! $matter->dead ? 'style="text-decoration: line-through;"' : '' !!}>
      <a class="bg-primary text-white" href="/matter?Ref={{ $matter->caseref }}" title="See family">{{ $matter->uid }}</a>
      ({{ $matter->category->category }})
      @canany(['admin', 'readwrite'])
      <a class="bg-primary text-white float-right hidden-action"
         data-toggle="modal" data-target="#ajaxModal" href="/matter/{{ $matter->id }}/edit" title="Advanced matter edition">
        &#9998;
      </a>
      @endcanany
    </div>
    <div class="card-body p-1">
      <dl class="row mb-0">
        @if ($matter->container_id)
        <dt class="col-4">Container:</dt>
        <dd class="col-8 mb-1">
          <a href="/matter/{{ $matter->container_id }}" title="See container">
            {{ $matter->container->uid }}
          </a>
        </dd>
        @endif
        @if ($matter->parent_id)
        <dt class="col-4">Parent:</dt>
        <dd class="col-8 mb-1">
          <a href="/matter/{{ $matter->parent_id }}" title="See parent">
            {{ $matter->parent->uid }}
          </a>
        </dd>
        @endif
        @if ($matter->expire_date)
        <dt class="col-4">Expiry:</dt>
        <dd class="col-8">{{ Carbon\Carbon::parse($matter->expire_date)->isoFormat('L') }}</dd>
        @endif
      </dl>
      <div class="alert alert-info text-center py-1 mb-0">
        <b>Responsible:</b>
        {{$matter->responsible}}
      </div>
    </div>
    <div class="card-footer p-1">
      @canany(['admin', 'readwrite'])
      <div class="btn-group btn-block">
        <a class="btn btn-info btn-sm" href="/matter/create?matter_id={{ $matter->id }}&operation=child" data-toggle="modal" data-target="#ajaxModal" data-size="modal-sm" title="Create child {{ $matter->category->category }}">
          &oplus; New Child
        </a>
        <a class="btn btn-info btn-sm" href="/matter/create?matter_id={{ $matter->id }}&operation=clone" data-toggle="modal" data-target="#ajaxModal" data-size="modal-sm" title="Clone {{ $matter->category->category }}">
          &boxbox; Clone
        </a>
        <a class="btn btn-info btn-sm {{ $matter->countryInfo->goesnational ? '' : 'disabled' }}" href="/matter/{{ $matter->id }}/createN" data-toggle="modal" data-target="#ajaxModal" data-size="modal-sm" title="Enter {{ $matter->category->category }} in national phase">
          &#9872; Nat. Phase
        </a>
      </div>
      @endcanany
    </div>
  </div>

  <div class="card col border-secondary p-1">
    <dl id="titlePanel">
      @foreach ( $titles as $type => $title_group )
        <dt class="mt-2">
          {{ $type }}
        </dt>
        @foreach ( $title_group as $title )
          <dd class="mb-0">
            <input data-resource="/classifier/{{ $title->id }}" class="titleItem noformat form-control text-truncate" name="value" value="{{ $title->value }}" autocomplete="off">
          </dd>
        @endforeach
      @endforeach
      <div>
        <a class="badge badge-pill badge-primary float-right" role="button" data-toggle="collapse" href="#addTitleCollapse">+</a>
      </div>
      <div id="addTitleCollapse" class="collapse">
        <form id="addTitleForm" autocomplete="off">
          <div class="form-row">
            <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
            <div class="col-2">
              <input type="hidden" name="type_code">
              <input type="text" class="form-control form-control-sm" data-ac="/classifier-type/autocomplete/1" data-actarget="type_code" data-aclength="0" placeholder="Type" autocomplete="off">
            </div>
            <div class="col-10">
              <div class="input-group">
                <input type="text" class="form-control form-control-sm" name="value" placeholder="Value" autocomplete="off">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-sm" id="addTitleSubmit">&check;</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </dl>
  </div>
  @if ( in_array('Image', $classifiers->keys()->all()) )
    <div class="card col-3 border-dark bg-dark p-1">
      <img src="/classifier/{{ $classifiers['Image'][0]->id }}/img" class="card-img-top" style="max-height: 150px; object-fit: contain;">
    </div>
  @endif
</div>

<div class="row card-deck">
  <div id="actorPanel" class="card col-3 border-secondary p-0" style="max-height: 600px">
    <div class="card-header reveal-hidden text-white bg-secondary p-1">
      Actors
      @canany(['admin', 'readwrite'])
      <a class="badge badge-pill badge-light hidden-action float-right" rel="popover" data-placement="right" href="#" title="Add Actor">
        &plus;
      </a>
      @endcanany
    </div>
    <div class="card-body bg-light p-1" style="overflow: auto;">
      @foreach ( $actors as $role_name => $role_group )
      <div class="card reveal-hidden border-secondary mb-1">
        <div class="card-header bg-primary text-light p-1">
          {{ $role_name }}
          @canany(['admin', 'readwrite'])
          <a class="hidden-action float-right text-light font-weight-bold ml-2" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" title="Edit actors in {{ $role_group->first()->role_name }} group" href="/matter/{{ $matter->id }}/roleActors/{{ $role_group->first()->role_code }}">
            &#9998;
          </a>
          <a class="hidden-action float-right text-light font-weight-bold" data-placement="right" rel="popover" title="Add {{ $role_name }}"
             data-role_name="{{ $role_name }}"
             data-role_code="{{ $role_group->first()->role_code }}"
             data-shareable="{{ $role_group->first()->shareable }}"
             href="#">
            &oplus;
          </a>
          @endcanany
        </div>
        <div class="card-body p-1" style="max-height: 80px; overflow: auto;">
          <ul class="list-unstyled mb-0">
            @foreach ( $role_group as $actor )
            <li class="text-truncate {{ $actor->inherited ? 'font-italic' : '' }}">
              @if ( $actor->warn )
              <span title="Special instructions">&#9888;</span>
              @endif
              <a @if ($actor->warn) class="text-danger" @endif
                href="/actor/{{ $actor->actor_id }}"
                data-toggle="modal"
                data-target="#ajaxModal"
                title="Actor data">
              {{ $actor->display_name }}
              </a>
              @if ( $actor->show_ref && $actor->actor_ref )
              ({{ $actor->actor_ref }})
              @endif
              @if ( $actor->show_company && $actor->company )
              &nbsp;- {{ $actor->company }}
              @endif
              @if ( $actor->show_date && $actor->date )
              ({{ Carbon\Carbon::parse($actor->date)->isoFormat('L') }})
              @endif
              @if ( $actor->show_rate && $actor->rate != '100' )
              &nbsp;- {{ $actor->rate }}
              @endif
            </li>
            @endforeach
          </ul>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  <div id="multiPanel" class="card col-9 p-0" style="background: transparent;">
    <div class="card-deck mb-1">
      <div class="card border-primary reveal-hidden" style="min-height: 138px;">
        <div class="card-header bg-primary p-1">
          <a class="row text-light text-decoration-none" href="/matter/{{ $matter->id }}/events" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" title="All events">
            <span class="col-5">Status</span>
            <span class="col-3">Date</span>
            <span class="col-4">
              Number
              <span class="hidden-action float-right">
                &equiv;
              </span>
            </span>
          </a>
        </div>
        <div class="card-body p-1" id="statusPanel" style="overflow: auto;">
          @foreach ( $matter->events->where('info.status_event', 1) as $event )
          <div class="row">
            <span class="col-5">{{ $event->info->name }}</span>
            @if ( $event->alt_matter_id )
            <span class="col-3">{{ Carbon\Carbon::parse($event->link->event_date ?? $event->event_date)->isoFormat('L') }}</span>
            <span class="col">
              <a href="/matter/{{ $event->alt_matter_id }}" title="{{ $event->altMatter->uid }}" target="_blank">{{ $event->altMatter->country }} {{ $event->link->detail ?? $event->detail }}</a>
            </span>
            @else
            <span class="col-3">{{ Carbon\Carbon::parse($event->event_date)->isoFormat('L') }}</span>
            <span class="col">
              @if ( $event->publicUrl() )
              <a href="{{ $event->publicUrl() }}" target="_blank">{{ $event->detail }}</a>
              @else
              {{ $event->detail }}
              @endif
            </span>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      <div class="card col-6 p-0 border-primary reveal-hidden">
        <div class="card-header {{ $matter->tasksPending->count() ? 'text-warning' : 'text-light' }} p-1 bg-primary">
          Open Tasks Due
          <a class="text-warning text-decoration-none hidden-action float-right stretched-link" href="/matter/{{ $matter->id }}/tasks" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" title="All tasks"><span class="">
            &equiv;
          </a>
        </div>
        <div class="card-body p-1" id="opentask-panel" style="overflow: auto;">
          @foreach ( $matter->tasksPending as $task )
          <div class="row">
            <span class="col-9">{{ $task->info->name }}: {{ $task->detail }}</span>
            <span class="col-3">{{ Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="row card-deck mb-1">
      <div class="card col-2 p-0 border-primary reveal-hidden" style="min-height: 138px;">
        <div class="card-header {{ $matter->renewalsPending->count() ? 'text-warning' : 'text-light' }} p-1 bg-primary">
          Renewals Due
          <a class="text-warning text-decoration-none hidden-action float-right stretched-link" href="/matter/{{ $matter->id }}/renewals" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" title="All renewals">
            &equiv;
          </a>
        </div>
        <div class="card-body p-1" id="renewal-panel" style="overflow: auto;">
          @foreach ( $matter->renewalsPending->take(3) as $task )
          <div class="row">
            <span class="col-4">{{ $task->detail }}</span>
            <span class="col-8">{{ Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}</span>
          </div>
          @endforeach
        </div>
      </div>
      <div class="card col-6 p-0 border-primary reveal-hidden">
        <div class="card-header p-1 bg-primary">
          <a class="row text-light text-decoration-none" href="/matter/{{ $matter->id }}/classifiers" data-target="#ajaxModal" data-toggle="modal" title="Classifier detail">
            <div class="col">
              Classifiers
              <span class="hidden-action float-right">
                &equiv;
              </span>
            </div>
          </a>
        </div>
        <div class="card-body p-1" id="classifierPanel" style="overflow: auto;">
          @foreach ( $classifiers as $type => $classifier_group )
            @if ( $type != 'Image' )
            <div class="card">
              <div class="card-body p-1">
                <span class="font-weight-bolder align-middle">{{ $type }}</span>
                @foreach ( $classifier_group as $classifier )
                  @if ( $classifier->url )
                    <a href="{{ $classifier->url }}" class="badge badge-primary align-middle" target="_blank">{{ $classifier->value }}</a>
                  @elseif ( $classifier->lnk_matter_id )
                    <a href="/matter/{{ $classifier->lnk_matter_id }}" class="badge badge-primary align-middle">{{ $classifier->linkedMatter->uid }}</a>
                  @else
                    <div class="badge badge-secondary align-middle">{{ $classifier->value }}</div>
                  @endif
                @endforeach
                @if ( $type == 'Link' )
                  @foreach ( $matter->linkedBy as $linkedBy )
                    <a href="/matter/{{ $linkedBy->id }}" class="badge badge-primary align-middle">{{ $linkedBy->uid }}</a>
                  @endforeach
                @endif
              </div>
            </div>
            @endif
          @endforeach
          @if ( !in_array('Link', $classifiers->keys()->all()) && !$matter->linkedBy->isEmpty() )
          <div class="card">
            <div class="card-body p-1">
              <span class="font-weight-bolder align-middle">Link</span>
                @foreach ( $matter->linkedBy as $linkedBy )
                  <a href="/matter/{{ $linkedBy->id }}" class="badge badge-primary align-middle">{{ $linkedBy->uid }}</a>
                @endforeach
            </div>
          </div>
          @endif
        </div>
      </div>
      <div class="card border-info col-4 p-0">
        <div class="card-header bg-info text-white p-1">
          Related Matters
          <span class="float-right">&#9432;</span>
        </div>
        <div class="card-body p-1" id="relationsPanel" style="overflow: auto;">
          @if ( $matter->family->count() )
          <dl class="mb-1">
            <dt>Fam</dt>
            <dd class="mb-1">
              @foreach ( $matter->family as $member )
              <a class="badge badge-primary" href="/matter/{{ $member->id }}">{{ $member->suffix }}</a>
              @endforeach
            </dd>
          </dl>
          @endif
          @foreach ( $matter->priorityTo->groupBy('caseref') as $caseref => $family )
          <dl class="mb-1">
            <dt>{{ $caseref }}</dt>
            <dd class="mb-1">
              @foreach ( $family as $rmatter )
              <a class="badge badge-primary" href="/matter/{{ $rmatter->id }}">{{ $rmatter->suffix }}</a>
              @endforeach
            </dd>
          </dl>
          @endforeach
        </div>
      </div>
    </div>
    <div class="card-deck">
      <div class="card border-secondary" style="min-height: 100px;">
        <div class="card-header p-1 bg-secondary text-light">
          Notes
        </div>
        <div class="card-body p-1" style="overflow: auto;">
          <textarea id="notes" class="form-control noformat" name="notes" data-resource="/matter/{{ $matter->id }}">{{ $matter->notes }}</textarea>
        </div>
        <div class="card-footer p-1">
          Summaries:
          <a class="badge badge-primary align-middle"
              href="/matter/{{ $matter->id }}/description/en"
              data-toggle="modal"
              data-target="#ajaxModal"
              data-size="modal-lg"
              title="Copy a summary in English">
              &boxbox; EN
          </a>
          <a class="badge badge-primary align-middle"
              href="/matter/{{ $matter->id }}/description/fr"
              data-toggle="modal"
              data-target="#ajaxModal"
              data-size="modal-lg"
              title="Copy a summary in French">
              &boxbox; FR
          </a>
          Email:
          <a class="badge badge-primary align-middle"
              href="/document/select/{{ $matter->id }}?Language=fra"
              data-toggle="modal"
              data-target="#ajaxModal"
              data-size="modal-lg"
              title="Prepare an email">
              &#9993; FR
          </a>
          <a class="badge badge-primary align-middle"
              href="/document/select/{{ $matter->id }}?Language=eng"
              data-toggle="modal"
              data-target="#ajaxModal"
              data-size="modal-lg"
              title="Prepare an email">
              &#9993; EN
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<template id="actorPopoverTemplate">
  <form id="addActorForm" autocomplete="off">
     <input type="hidden" name="role">
     <input type="hidden" name="shared">
     <input type="hidden" name="actor_id">
     <div class="ui-front">
       <input type="text" class="form-control form-control-sm" id="roleName" data-actarget="role" placeholder="Role">
       <input type="text" class="form-control form-control-sm" id="actorName" data-actarget="actor_id" placeholder="Name">
       <input type="text" class="form-control form-control-sm" name="actor_ref" placeholder="Reference">
     </div>
     <div class="form-group">
       <div class="form-check my-1">
         <input class="form-check-input mt-0" type="radio" id="actorShared" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
         <label class="form-check-label" for="actorShared">Add to container and share</label>
       </div>
       <div class="form-check my-1">
         <input class="form-check-input mt-0" type="radio" id="actorNotShared" name="matter_id" value="{{ $matter->id }}">
         <label class="form-check-label" for="actorNotShared">Add to this matter only (not shared)</label>
       </div>
     </div>
     <div class="btn-group" role="group">
       <button type="button" class="btn btn-info btn-sm" id="addActorSubmit">&check;</button>
       <button type="button" class="btn btn-outline-info btn-sm" id="popoverCancel">&times;</button>
     </div>
     <div class="alert alert-danger d-none" role="alert"></div>
   </form>
</template>

@stop

@section('script')

@include('matter.show-js')

@stop
