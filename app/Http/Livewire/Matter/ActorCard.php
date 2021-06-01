<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\Matter;

class ActorCard extends Component
{
    protected $listeners = ['refreshActorCard'];
    public $container_id;
    public $role_group;
        
    public function refreshActorCard()
    {
        $this->role_group = Matter::find($this->role_group->first()->matter_id)->actors()->whereRoleCode($this->role_group->first()->role_code)->get();
    }

    public function render()
    {
        return view('livewire.matter.actor-card');
    }
}
