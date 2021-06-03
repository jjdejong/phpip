<?php
/*
    This is a generic component that should be extended by a specialized component for each autocompletion source.
    Each specialized component defines the query() function to return a list of parameters "id", "name", and "extra"
    where "name" is the name to be displayed, "id" is the primary key, and "extra" is an additional parameter
    that may be used by the parent component.

    This component emits the "autoCompleted" event with the "id", "name", and "extra" parameters of the selected item,
    and a "source" parameter. A parent component should listen to that event to deal as required with the parameters.
    The "source" parameter helps the parent component recognize what specialized component has emitted the event.

    When the "search" field is prefilled through @livewire('specialized-autocomplete', ['search => value]) in the parent
    component, and then erased by the user, this means the user wishes to nullout the field. Then, an "autoCompleted" event
    is emitted with all parameters null (except "source").

    Autocompletion fields that have a required value should only have their placeholder prefilled like so:
    @livewire('specialized-autocomplete', ['placeholder => value]), which prevents the user from erasing the field.
 */

namespace App\Http\Livewire;

use Livewire\Component;

abstract class Autocomplete extends Component
{
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
            if (strlen($this->search) == 0) {
                $this->emitUp('autoCompleted', null, null, null, $this->source);
            }
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
        $this->emitUp('autoCompleted', $id, $name, $extra, $this->source);
    }

    public function render()
    {
        return view('livewire.autocomplete');
    }
}
