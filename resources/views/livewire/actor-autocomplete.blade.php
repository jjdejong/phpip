<div>
    <div
      x-data="{
        open: @entangle('showDropdown'),
        search: @entangle('search'),
        selected: @entangle('selected'),
        highlightedIndex: 0,
        highlightPrevious() {
          if (this.highlightedIndex > 0) {
            this.highlightedIndex = this.highlightedIndex - 1;
            this.scrollIntoView();
          }
        },
        highlightNext() {
          if (this.highlightedIndex < this.$refs.results.children.length - 1) {
            this.highlightedIndex = this.highlightedIndex + 1;
            this.scrollIntoView();
          }
        },
        scrollIntoView() {
          this.$refs.results.children[this.highlightedIndex].scrollIntoView({
            block: 'nearest',
            behavior: 'smooth'
          });
        },
        updateSelected(id, name) {
          this.selected = id;
          this.search = name;
          this.open = false;
          this.highlightedIndex = 0;
        },
    }">
    <div
      x-on:value-selected="updateSelected($event.detail.id, $event.detail.name)">
      <span>
        <div>
          <input
            wire:model.debounce.300ms="search"
            x-on:keydown.arrow-down.stop.prevent="highlightNext()"
            x-on:keydown.arrow-up.stop.prevent="highlightPrevious()"
            x-on:keydown.enter.stop.prevent="$dispatch('value-selected', {
              id: $refs.results.children[highlightedIndex].getAttribute('data-result-id'),
              name: $refs.results.children[highlightedIndex].getAttribute('data-result-name')
            })">
        </div>
      </span>
  
      <div
        x-show="open"
        x-on:click.away="open = false">
          <ul x-ref="results">
            @forelse($results as $index => $result)
              <li
                wire:key="{{ $index }}"
                x-on:click.stop="$dispatch('value-selected', {
                  id: {{ $result->id }},
                  name: '{{ $result->name }}'
                })"
                :class="{
                  'bg-primary': {{ $index }} === highlightedIndex,
                  'text-white': {{ $index }} === highlightedIndex
                }"
                data-result-id="{{ $result->id }}"
                data-result-name="{{ $result->name }}">
                  <span>
                    {{ $result->name }}
                  </span>
              </li>
            @empty
              <li>No results found</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
