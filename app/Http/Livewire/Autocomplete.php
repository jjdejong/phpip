<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class Autocomplete extends Component
{
    public $results;
    public $search;
    public $placeholder;
    public $inputClass = 'form-control';

    abstract public function query();

    public function mount()
    {
        $this->results = collect();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->results = collect();
        } else {
            $this->results = $this->query()->get();
        }
    }

    public function selectedItem($id, $name)
    {
        $this->search = '';
        $this->placeholder = $name;
        $this->results = collect();
        $this->emitUp('autoComplete', $id);
    }

    public function render()
    {
        return view('livewire.autocomplete');
    }
}
