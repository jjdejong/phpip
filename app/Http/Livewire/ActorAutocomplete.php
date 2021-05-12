<?php

namespace App\Http\Livewire;

use App\Actor;

class ActorAutocomplete extends Autocomplete
{
    public function query()
    {
        return Actor::where('name', 'like', $this->search.'%')->orderBy('name');
    }
}
