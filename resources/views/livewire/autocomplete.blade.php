<div style="position: relative">
    <input wire:model="search" class="{{ $inputClass }}" type="text" placeholder="{{ $placeholder }}" id="{{ $placeholder }}">
    @if(count($results) > 0)
    <div class="card" style="position: absolute; z-index: 100"> 
        @foreach($results as $item)
        <button class="dropdown-item" wire:click.prevent="selectedItem('{{ $item->id }}', '{{ $item->name }}')" wire:key="{{ $item->id }}">
            {{ $item->id }}: {{ $item->name }}
        </button>
        @endforeach
    </div>
    @endif
</div>