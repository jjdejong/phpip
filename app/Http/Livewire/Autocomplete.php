<?php
/*
    This is a generic component that should be extended by a specialized component for each autocompletion source.
    Each specialized component defines the query() function to return a list of parameters "id", "name", and "extra"
    where "name" is the full name as displayed, "id" is the primary key, and "extra" is an additional parameter
    that may be used by the parent component.

    This component emits the "autoCompleted" event with the "id", "name", "extra" and "placeholder" parameters of the selected item.
    A parent component should listen to that event to deal as required with the parameters. The "placeholder" parameter
    helps the parent component recognize what autocompletion component has emitted the event.
 */
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
            $this->results = $this->query()->take(20)->get();
        }
    }

    public function selectedItem($id, $name, $extra)
    {
        // Set the input value to the full name
        $this->search = $name;
        // Empty the results list
        $this->results = collect();
        // Warn parent component of autocompletion
        $this->emitUp('autoCompleted', $id, $name, $extra, $this->placeholder);
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
