<div class="card reveal-hidden border-secondary mb-1">
    <div class="card-header bg-primary text-light p-1 clearfix">
        {{ $role_group->first()->role_name }}
        @canany(['admin', 'readwrite'])
        <a wire:click.prevent="$toggle('editActive')" class="hidden-action badge badge-pill badge-light float-right" 
            title="Edit actors in {{ $role_group->first()->role_name }} group" href="#">
            &#9998;
        </a>
        @if ($editActive)
            @livewire('matter.actor-card-edit', ['role_group' => $role_group], key('edit-'.$role_group->first()->role_code))
        @endif
        <a wire:click.prevent="$toggle('addActive')" class="hidden-action badge badge-pill badge-light float-right" 
            title="Add {{ $role_group->first()->role_name }}" href="#">
            &plus;
        </a>
        @if ($addActive)
            @livewire('matter.actor-add', [
                'matter_id' => $role_group->first()->matter_id,
                'container_id' => $container_id,
                'role_name' => $role_group->first()->role_name,
                'role_code' => $role_group->first()->role_code,
                'role_shareable' => $role_group->first()->shareable,
            ], key('add-'.$role_group->first()->role_name))
        @endif
        @endcanany
    </div>
    <div class="card-body p-1" style="max-height: 5rem; overflow: auto;">
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
