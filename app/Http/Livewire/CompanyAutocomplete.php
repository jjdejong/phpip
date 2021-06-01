<?php

namespace App\Http\Livewire;

use App\Actor;

class CompanyAutocomplete extends Autocomplete
{
    public $length = 2;
    public $source = 'company';
    
    public function query()
    {
        return Actor::select('name', 'id', 'parent_id as extra')
            ->where('name', 'like', $this->search.'%')
            ->wherePhyPerson(0)
            ->orderBy('name');
    }
}
