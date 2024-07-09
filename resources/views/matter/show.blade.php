@php
$titles = $matter->titles->groupBy('type_name');
$classifiers = $matter->classifiers->groupBy('type_name');
$actors = $matter->actors->groupBy('role_name');
$linkedBy = $matter->linkedBy->groupBy('type_code');
@endphp

@extends('layouts.app')

@section('content')
<div class="row g-1 mb-1">
  <div class="col-3">
    <div id="refsPanel" class="card border-primary p-0 h-100">
      <div class="card-header bg-primary text-light reveal-hidden p-1">
        <a class="bg-primary text-white lead {{ $matter->dead ? 'text-decoration-line-through' : '' }}" href="/matter?Ref={{ $matter->caseref }}" title="See family">{{ $matter->uid }}</a>
        ({{ $matter->category->category }})
        @canany(['admin', 'readwrite'])
        <a class="bg-primary text-white float-end hidden-action"
          data-bs-toggle="modal" data-bs-target="#ajaxModal" href="/matter/{{ $matter->id }}/edit" title="Advanced matter edition">
          <svg width="14" height="14" fill="currentColor"><use xlink:href="#pencil-square"/></svg>
        </a>
        @endcanany
      </div>
      <div class="card-body p-1">
        <dl class="row mb-0">
          @if ($matter->container_id)
          <dt class="col-4 text-end">Container:</dt>
          <dd class="col-8 mb-0">
            <a href="/matter/{{ $matter->container_id }}" title="See container">
              {{ $matter->container->uid }}
            </a>
          </dd>
          @endif
          @if ($matter->parent_id)
          <dt class="col-4 text-end">Parent:</dt>
          <dd class="col-8 mb-0">
            <a href="/matter/{{ $matter->parent_id }}" title="See parent">
              {{ $matter->parent->uid }}
            </a>
          </dd>
          @endif
          @if ($matter->alt_ref)
          <dt class="col-4 text-end">Alt. ref:</dt>
          <dd class="col-8 mb-0">{{ $matter->alt_ref }}</dd>
          @endif
          @if ($matter->expire_date)
          <dt class="col-4 text-end">Expiry:</dt>
          <dd class="col-8">{{ Carbon\Carbon::parse($matter->expire_date)->isoFormat('L') }}</dd>
          @endif
        </dl>
        <div class="alert alert-info text-center py-1 mb-0">
          <b>Responsible:</b>
          {{$matter->responsible}}
        </div>
      </div>
      <div class="card-footer d-grid gap-2 p-1">
        @canany(['admin', 'readwrite'])
        <div class="btn-group">
          <a class="btn btn-info btn-sm" href="/matter/create?matter_id={{ $matter->id }}&operation=child" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-sm" title="Create child {{ $matter->category->category }}">
            <svg width="14" height="14" fill="currentColor"><use xlink:href="#node-plus-fill"/></svg> New Child
          </a>
          <a class="btn btn-info btn-sm" href="/matter/create?matter_id={{ $matter->id }}&operation=clone" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-sm" title="Clone {{ $matter->category->category }}">
            &boxbox; Clone
          </a>
          <a class="btn btn-info btn-sm {{ $matter->countryInfo->goesnational ? '' : 'disabled' }}" href="/matter/{{ $matter->id }}/createN" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-sm" title="Enter {{ $matter->category->category }} in national phase">
            <svg width="14" height="14" fill="currentColor"><use xlink:href="#flag-fill"/></svg> Nat. Phase
          </a>
        </div>
        @endcanany
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card border-secondary p-1 h-100">
      <dl id="titlePanel">
        @foreach ( $titles as $type => $title_group )
          <dt class="mt-2">
            {{ $type }}
          </dt>
          @foreach ( $title_group as $title )
            <dd class="mb-0" data-resource="/classifier/{{ $title->id }}" data-name="value" contenteditable>
              {{ $title->value }}
            </dd>
          @endforeach
        @endforeach
        <div>
          <a class="badge rounded-pill text-bg-primary float-end" role="button" data-bs-toggle="collapse" href="#addTitleCollapse">+</a>
        </div>
        <div id="addTitleCollapse" class="collapse">
          <form id="addTitleForm" autocomplete="off">
            <div class="row">
              <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
              <div class="col-2">
                <input type="hidden" name="type_code">
                <input type="text" class="form-control form-control-sm" data-ac="/classifier-type/autocomplete/1" data-actarget="type_code" data-aclength="0" placeholder="Type" autocomplete="off">
              </div>
              <div class="col-10">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="value" placeholder="Value" autocomplete="off">
                  <button type="button" class="btn btn-primary btn-sm" id="addTitleSubmit">&check;</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </dl>
    </div>
  </div>
  @if ( in_array('Image', $classifiers->keys()->all()) )
  <div class="col-3">
    <div class="card border-dark bg-dark p-1">
      <img src="/classifier/{{ $classifiers['Image'][0]->id }}/img" class="card-img-top" style="max-height: 150px; object-fit: contain;">
    </div>
  </div>
  @endif
</div>

<div class="row g-1">
  <div class="col-3">
    <div id="actorPanel" class="card border-secondary h-100" style="max-height: 600px">
      <div class="card-header reveal-hidden text-white bg-secondary">
        Actors
        @canany(['admin', 'readwrite'])
        <a class="hidden-action text-light fw-bold float-end" data-bs-toggle="popover" href="javascript:void(0)" title="Add Actor">
          <svg width="14" height="14" fill="currentColor"><use xlink:href="#person-plus-fill"/></svg>
        </a>
        @endcanany
      </div>
      <div class="card-body bg-light p-1" style="overflow: auto;">
        @foreach ( $actors as $role_name => $role_group )
        <div class="card reveal-hidden border-secondary mb-1">
          <div class="card-header bg-primary text-light p-1">
            {{ $role_name }}
            @canany(['admin', 'readwrite'])
            <a class="hidden-action float-end text-light fw-bold ms-3" data-bs-toggle="popover" title="Add {{ $role_name }}"
              data-role_name="{{ $role_name }}"
              data-role_code="{{ $role_group->first()->role_code }}"
              data-shareable="{{ $role_group->first()->shareable }}"
              href="javascript:void(0)">
              <svg width="12" height="12" fill="currentColor"><use xlink:href="#person-plus-fill"/></svg>
            </a>
            <a class="hidden-action float-end text-light" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-lg" title="Edit actors in {{ $role_group->first()->role_name }} group" href="/matter/{{ $matter->id }}/roleActors/{{ $role_group->first()->role_code }}">
              <svg width="12" height="12" fill="currentColor"><use xlink:href="#pencil-square"/></svg>
            </a>
            @endcanany
          </div>
          <div class="card-body p-1" style="max-height: 80px; overflow: auto;">
            <ul class="list-unstyled mb-0">
              @foreach ( $role_group as $actor )
              <li class="text-truncate {{ $actor->inherited ? 'fst-italic' : '' }}">
                @if ( $actor->warn && $actor->role_code == 'CLI')
                <span class="text-danger" title="Special instructions">
                  <svg width="12" height="12" fill="currentColor"><use xlink:href="#exclamation-triangle-fill"/></svg>
                </span>
                @endif
                <a @if ($actor->warn && $actor->role_code == 'CLI') class="text-danger" @endif
                  href="/actor/{{ $actor->actor_id }}"
                  data-bs-toggle="modal"
                  data-bs-target="#ajaxModal"
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
  </div>
  <div class="col-9">
    <div id="multiPanel" class="container p-0">
      <div class="row mb-1 g-1" style="min-height: 138px;">
        <div class="col">
          <div class="card p-0 border-primary reveal-hidden h-100">
            <div class="card-header bg-primary p-1">
              <a class="row text-light text-decoration-none" href="/matter/{{ $matter->id }}/events" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-lg" title="All events">
                <span class="col-5">Status</span>
                <span class="col-3">Date</span>
                <span class="col-4">
                  Number
                  <span class="hidden-action float-end">
                    &equiv;
                  </span>
                </span>
              </a>
            </div>
            <div class="card-body p-1" id="statusPanel" style="overflow: auto;">
              @foreach ( $matter->events->where('info.status_event', 1) as $event )
              <div class="row g-0">
                <span class="col-5">{{ $event->info->name }}</span>
                @if ( $event->alt_matter_id )
                <span class="col-3">{{ ($event->link->event_date ?? $event->event_date)->isoFormat('L') }}</span>
                <span class="col">
                  <a href="/matter/{{ $event->alt_matter_id }}" title="{{ $event->altMatter->uid }}" target="_blank">{{ $event->altMatter->country }} {{ $event->link->detail ?? $event->detail }}</a>
                </span>
                @else
                <span class="col-3">{{ $event->event_date->isoFormat('L') }}</span>
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
        </div>
        <div class="col">
          <div class="card p-0 border-primary reveal-hidden h-100">
            <div class="card-header {{ $matter->tasksPending->count() ? 'text-warning' : 'text-light' }} p-1 bg-primary">
              Open Tasks Due
              <a class="text-warning text-decoration-none hidden-action float-end stretched-link" href="/matter/{{ $matter->id }}/tasks" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-lg" title="History"><span class="">
                &equiv;
              </a>
            </div>
            <div class="card-body p-1" id="opentask-panel" style="overflow: auto;">
              @foreach ( $matter->tasksPending as $task )
              <div class="row">
                <span class="col-9">{{ $task->info->name }}: {{ $task->detail }}</span>
                <span class="col-3">{{ $task->due_date->isoFormat('L') }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-1 g-1" style="min-height: 138px;">
        <div class="col-2">
          <div class="card p-0 border-primary reveal-hidden h-100">
            <div class="card-header {{ $matter->renewalsPending->count() ? 'text-warning' : 'text-light' }} p-1 bg-primary">
              Renewals Due
              <a class="text-warning text-decoration-none hidden-action float-end stretched-link" href="/matter/{{ $matter->id }}/renewals" data-bs-toggle="modal" data-bs-target="#ajaxModal" data-size="modal-lg" title="All renewals">
                &equiv;
              </a>
            </div>
            <div class="card-body p-1" id="renewal-panel" style="overflow: auto;">
              @foreach ( $matter->renewalsPending->take(3) as $task )
              <div class="row">
                <span class="col-4">{{ $task->detail }}</span>
                <span class="col-8">{{ $task->due_date->isoFormat('L') }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card p-0 border-primary reveal-hidden h-100">
            <div class="card-header p-1 bg-primary">
              <a class="row text-light text-decoration-none" href="/matter/{{ $matter->id }}/classifiers" data-bs-target="#ajaxModal" data-bs-toggle="modal" title="Classifier detail">
                <div class="col">
                  Classifiers
                  <span class="hidden-action float-end">
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
                    <span class="fw-bolder align-middle">{{ $type }}</span>
                    @foreach ( $classifier_group as $classifier )
                      @if ( $classifier->url )
                        <a href="{{ $classifier->url }}" class="badge fw-normal text-bg-primary align-middle" target="_blank">{{ $classifier->value }}</a>
                      @elseif ( $classifier->lnk_matter_id )
                        <a href="/matter/{{ $classifier->lnk_matter_id }}" class="badge fw-normal text-bg-primary align-middle">{{ $classifier->linkedMatter->uid }}</a>
                      @else
                        <div class="badge fw-normal text-bg-secondary align-middle">{{ $classifier->value }}</div>
                      @endif
                    @endforeach
                    @if ( $type == 'Link' )
                      @foreach ( $matter->linkedBy as $linkedBy )
                        <a href="/matter/{{ $linkedBy->id }}" class="badge fw-normal text-bg-primary align-middle">{{ $linkedBy->uid }}</a>
                      @endforeach
                    @endif
                  </div>
                </div>
                @endif
              @endforeach
              @if ( !in_array('Link', $classifiers->keys()->all()) && !$matter->linkedBy->isEmpty() )
              <div class="card">
                <div class="card-body p-1">
                  <span class="fw-bolder align-middle">Link</span>
                    @foreach ( $matter->linkedBy as $linkedBy )
                      <a href="/matter/{{ $linkedBy->id }}" class="badge fw-normal text-bg-primary align-middle">{{ $linkedBy->uid }}</a>
                    @endforeach
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="card border-info p-0 h-100">
            <div class="card-header bg-info text-white p-1">
              Related Matters
              <span class="float-end">&#9432;</span>
            </div>
            <div class="card-body p-1" id="relationsPanel" style="overflow: auto;">
              @if ( $matter->family->count() )
              <dl class="mb-1">
                <dt>Fam</dt>
                <dd class="mb-1">
                  @foreach ( $matter->family as $member )
                  <a class="badge fw-normal text-bg-{{ $member->suffix == $matter->suffix ? 'secondary' : 'primary' }}" href="/matter/{{ $member->id }}">{{ $member->suffix }}</a>
                  @endforeach
                </dd>
              </dl>
              @endif
              @foreach ( $matter->priorityTo->groupBy('caseref') as $caseref => $family )
              <dl class="mb-1">
                <dt>{{ $caseref }}</dt>
                <dd class="mb-1">
                  @foreach ( $family as $rmatter )
                  <a class="badge text-bg-primary fw-normal" href="/matter/{{ $rmatter->id }}">{{ $rmatter->suffix }}</a>
                  @endforeach
                </dd>
              </dl>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      <div class="row g-1" style="min-height: 100px;">
        <div class="col-10">
          <div class="card border-secondary p-0 h-100">
            <div class="card-header p-1 bg-secondary text-light">
              Notes
            </div>
            <div class="card-body p-1" style="overflow: auto;">
              <textarea id="notes" class="form-control noformat" name="notes" data-resource="/matter/{{ $matter->id }}">{{ $matter->notes }}</textarea>
            </div>
            <div class="card-footer p-1">
              Summaries:
              <a class="badge text-bg-primary align-middle"
                  href="/matter/{{ $matter->id }}/description/en"
                  data-bs-toggle="modal"
                  data-bs-target="#ajaxModal"
                  data-size="modal-lg"
                  title="Copy a summary in English">
                  &boxbox; EN
              </a>
              <a class="badge text-bg-primary align-middle"
                  href="/matter/{{ $matter->id }}/description/fr"
                  data-bs-toggle="modal"
                  data-bs-target="#ajaxModal"
                  data-size="modal-lg"
                  title="Copy a summary in French">
                  &boxbox; FR
              </a>
              Email:
              <a class="badge text-bg-primary align-middle"
                  href="/document/select/{{ $matter->id }}?Language=en"
                  data-bs-toggle="modal"
                  data-bs-target="#ajaxModal"
                  data-size="modal-lg"
                  title="Send email">
                  &#9993; EN
              </a>
              <a class="badge text-bg-primary align-middle"
                  href="/document/select/{{ $matter->id }}?Language=fr"
                  data-bs-toggle="modal"
                  data-bs-target="#ajaxModal"
                  data-size="modal-lg"
                  title="Envoyer un courriel">
                  &#9993; FR
              </a>
            </div>
          </div>
        </div>
        <div class="col-2">
          <div class="card h-100">
            <div id="dropZone" class="card-body bg-info text-light text-center align-center" data-url="/matter/{{ $matter->id }}/mergeFile">
              <svg width="18" height="18" class="my-1" fill="currentColor"><use xlink:href="#intersect"/></svg>
              <div class="mb-3">Drop File to Merge</div>
              <a class="text-primary" href="https://github.com/jjdejong/phpip/wiki/Templates-(email-and-documents)#document-template-usage" target="_blank">
                <svg width="16" height="16" fill="currentColor"><use xlink:href="#question-circle-fill"/></svg>
              </a>
            </div>
          </div>
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
      <input type="text" class="form-control form-control-sm" id="roleName" data-ac="/role/autocomplete" data-actarget="role" placeholder="Role">
      <input type="text" class="form-control form-control-sm" id="actorName" data-ac="/actor/autocomplete/1" data-actarget="actor_id" placeholder="Name">
      <input type="text" class="form-control form-control-sm" name="actor_ref" placeholder="Reference">
      <div class="form-check my-1">
          <input class="form-check-input mt-0" type="radio" id="actorShared" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}">
          <label class="form-check-label" for="actorShared">Add to container and share</label>
      </div>
      <div class="form-check my-1">
          <input class="form-check-input mt-0" type="radio" id="actorNotShared" name="matter_id" value="{{ $matter->id }}">
          <label class="form-check-label" for="actorNotShared">Add to this matter only (not shared)</label>
      </div>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-info btn-sm" id="addActorSubmit">&check;</button>
        <button type="button" class="btn btn-outline-info btn-sm" id="popoverCancel">&times;</button>
      </div>
      <div class="alert alert-danger d-none" role="alert"></div>
   </form>
</template>

@endsection

@section('script')
<script src="{{ asset('js/matter-show.js') }}" defer></script>
@endsection
