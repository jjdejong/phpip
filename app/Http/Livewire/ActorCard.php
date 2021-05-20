<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ActorCard extends Component
{
    // protected $listeners = ['actorAdded'];
    public $role_name;
    public $matter_id;
    public $container_id;
    public $role_group;
        
    // public function actorAdded()
    // {
    //     $this->render();
    // }

    public function render()
    {
        return view('livewire.actor-card');
    }
}
