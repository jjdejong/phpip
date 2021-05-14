<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class Autocomplete extends Component
{
    public $results;
    public $search;
    public $placeholder = "Find...";
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
        $this->search = $name;
        $this->results = collect();
        $this->emitUp('autoCompleted', $id, $this->placeholder);
    }

    public function render()
    {
        return view('livewire.autocomplete');
    }
}
