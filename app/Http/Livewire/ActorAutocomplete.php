<?php

namespace App\Http\Livewire;

use App\Actor;

class ActorAutocomplete extends Autocomplete
{
    public $length = 2;
    
    public function query()
    {
        return Actor::where('name', 'like', $this->search.'%')->orderBy('name');
    }
}
