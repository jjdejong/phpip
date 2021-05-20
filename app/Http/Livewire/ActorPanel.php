<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Matter;

class ActorPanel extends Component
{
    protected $listeners = ['actorAdded'];
    public $matter_id;
    public $container_id;
    public $actors;
    
    public function mount()
    {
        $this->actors = Matter::find($this->matter_id)->actors->groupBy('role_name')->toBase();
    }
    
    public function actorAdded()
    {
        $this->actors = Matter::find($this->matter_id)->actors->groupBy('role_name')->toBase();
    }
    
    public function render()
    {
        return view('livewire.actor-panel');
    }
}
