<?php

namespace App\Http\Livewire;

use App\Role;

class RoleAutocomplete extends Autocomplete
{
    public $length = 1;
    
    public function query()
    {
        return Role::select('code as id', 'name')->where('name', 'like', $this->search.'%')->orderBy('name');
    }
}
