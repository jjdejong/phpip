<?php

namespace App\Http\Livewire;

use App\Actor;

class ActorAutocomplete extends Autocomplete
{
    protected $listeners = ['valueSelected'];

    public function valueSelected(Actor $actor)
    {
        $this->emitUp('actorSelected', $actor);
    }

    public function query()
    {
        return Actor::where('name', 'like', $this->search.'%')->orderBy('name');
    }
}
