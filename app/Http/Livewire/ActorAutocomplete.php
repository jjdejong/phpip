<?php

namespace App\Http\Livewire;

use App\Actor;

class ActorAutocomplete extends Autocomplete
{
    public $length = 2;
    public $source = 'actor';
    
    public function query()
    {
        return Actor::select('name', 'id', 'company_id as extra')->where('name', 'like', $this->search.'%')->orderBy('name');
    }
}
