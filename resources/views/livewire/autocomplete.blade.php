<div style="position:relative">
    <input wire:model="search" class="form-control" type="text" placeholder="Search..." />
    <div style="position:absolute; z-index:100">
        @if(count($results) > 0)
        <div class="card">
            @foreach($results as $item)
            <button class="dropdown-item" wire:click.prevent="selectedItem({{ $item->id }}, '{{ $item->name }}')" :key="{{ $item->id }}">
                {{ $item->id }}: {{ $item->name }}
            </button>
            @endforeach
        </div>
        @endif
    </div>
</div>