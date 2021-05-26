<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class Autocomplete extends Component
{
    protected $listeners = ['resetAutoComplete'];
    // The search results
    public $results;
    // The entered search string
    public $search = '';
    // Default placeholder, to be used for diffing multiple autocompletion sources
    public $placeholder = "Find...";
    // Default classes to apply to input field (from Bootstrap)
    public $inputClass = 'form-control';

    abstract public function query();

    public function mount()
    {
        $this->results = collect();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < $this->length) {
            $this->results = collect();
        } else {
            $this->results = $this->query()->get();
        }
    }

    public function selectedItem($id, $name)
    {
        // Set the input value ('search') to the actor name
        $this->search = $name;
        // Empty the results list
        $this->results = collect();
        // The top component listens to the "autoCompleted" event and uses
        // "placeholder" to distinguish from multiple autocompletion sources
        $this->emitUp('autoCompleted', $id, $this->placeholder);
    }

    public function resetAutoComplete()
    {
        $this->reset('search');
    }

    public function render()
    {
        return view('livewire.autocomplete');
    }
}
