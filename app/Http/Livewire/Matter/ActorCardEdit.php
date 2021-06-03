<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;

class ActorCardEdit extends Component
{
    protected $listeners = ['actorChanged'];
    public $role_group;
    public $role_name;

    public function actorChanged()
    {
        $this->role_group = \App\Matter::find($this->role_group->first()->matter_id)->actors()->whereRoleName($this->role_name)->get();
    }

    public function render()
    {
        return view('livewire.matter.actor-card-edit');
    }
}
