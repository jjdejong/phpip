<div style="position: relative">
    <input wire:model.debounce.300ms="search" class="{{ $inputClass }}" type="text" placeholder="{{ $placeholder }}" autocomplete="off">
    @if(count($results) > 0)
    <div class="card shadow" style="position: absolute; z-index: 100; max-height: 8rem; overflow: auto">
        @foreach($results as $item)
        <button class="dropdown-item" wire:click.prevent="selectedItem('{{ $item->id }}', '{{ $item->name }}', '{{ $item->extra }}')" wire:key="{{ $item->id }}">
            {{ $item->id }}: {{ $item->name }}
        </button>
        @endforeach
    </div>
    @endif
</div>