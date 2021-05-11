  <div>
    <div
      x-data="{
        open: @entangle('showDropdown'),
        search: @entangle('search'),
        selected: @entangle('selected'),
        updateSelected(id, name) {
          this.selected = id;
          this.search = name;
          this.open = false;
        },
      }">
      <div x-on:value-selected="updateSelected($event.detail.id, $event.detail.name)">
        <input class="form-control" type="text" wire:model="search">
        <div x-show="open" x-on:click.away="open = false" style="position:absolute; z-index:100">
            <div x-ref="results" class="card">
              @foreach($results as $result)
                <button class="dropdown-item" type="button"
                  wire:key="{{ $result->id }}"
                  x-on:click.stop="$dispatch('value-selected', {
                    id: {{ $result->id }},
                    name: '{{ $result->name }}'
                  })">
                  {{ $result->name }}
                </button>
              @endforeach
            </div>
        </div>
      </div>
    </div>
  </div>
