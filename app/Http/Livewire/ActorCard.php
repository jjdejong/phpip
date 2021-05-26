<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Matter;

class ActorCard extends Component
{
    protected $listeners = ['actorAdded'];
    public $role_name;
    public $matter_id;
    public $container_id;
    public $role_group;
        
    public function actorAdded()
    {
        $this->role_group = Matter::find($this->matter_id)->actors()->whereRoleName($this->role_name)->get()->toBase();
    }

    public function render()
    {
        return view('livewire.actor-card');
    }
}
