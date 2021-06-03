<?php

namespace App\Http\Livewire;

use App\Role;

class RoleAutocomplete extends Autocomplete
{
    public $length = 1;
    public $source = 'role';
    
    public function query()
    {
        return Role::select('code as id', 'name', 'shareable as extra')
            ->where('name', 'like', $this->search.'%')->orderBy('name')
            ->orWhere('code', 'like', $this->search.'%')->orderBy('name');
    }
}
