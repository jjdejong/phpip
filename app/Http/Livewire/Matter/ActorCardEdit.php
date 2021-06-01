<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\Matter;

class ActorCardEdit extends Component
{
    protected $listeners = ['refreshActorCard'];
    public $role_group;
    
    public function mount()
    {
        $this->role_group = Matter::find($this->role_group->first()->matter_id)->actors()->whereRoleCode($this->role_group->first()->role_code)->get();
    }

    public function refreshActorCard()
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.matter.actor-card-edit');
    }
}
