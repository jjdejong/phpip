<div class="card reveal-hidden border-secondary mb-1">
    <div class="card-header bg-primary text-light p-1">
        {{ $role_name }}
        @canany(['admin', 'readwrite'])
        @livewire('add-actor', [
            'matter_id' => $matter_id,
            'container_id' => $container_id,
            'role_name' => $role_name,
        ], key($role_name))
        <a class="hidden-action float-right text-light font-weight-bold" data-toggle="modal" data-target="#ajaxModal" data-size="modal-lg" title="Edit actors in {{ $role_name }} group" href="/matter/{{ $matter_id }}/roleActors/{{ $role_group->first()->role_code }}">
            &#9998;
        </a>
        @endcanany
    </div>
    <div class="card-body p-1" style="max-height: 80px; overflow: auto;">
        <ul class="list-unstyled mb-0">
        @foreach ($role_group as $actor)
            <li class="text-truncate {{ $actor->inherited ? 'font-italic' : '' }}">
            @if ($actor->warn)
                <span title="Special instructions">&#9888;</span>
            @endif
                <a @if ($actor->warn) class="text-danger" @endif
                    href="/actor/{{ $actor->actor_id }}"
                    data-toggle="modal"
                    data-target="#ajaxModal"
                    title="Actor data">
                {{ $actor->display_name }}
                </a>
            @if ($actor->show_ref && $actor->actor_ref)
                ({{ $actor->actor_ref }})
            @endif
            @if ($actor->show_company && $actor->company)
                &nbsp;- {{ $actor->company }}
            @endif
            @if ($actor->show_date && $actor->date)
                ({{ Carbon\Carbon::parse($actor->date)->isoFormat('L') }})
            @endif
            @if ($actor->show_rate && $actor->rate != '100')
                &nbsp;- {{ $actor->rate }}
            @endif
            </li>
        @endforeach
        </ul>
    </div>
</div>
