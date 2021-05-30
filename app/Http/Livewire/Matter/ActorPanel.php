<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\Matter;

class ActorPanel extends Component
{
    protected $listeners = ['actorsChanged'];
    public $matter_id;
    public $container_id;
    public $actors;
    
    public function mount()
    {
        $this->actors = Matter::find($this->matter_id)->actors->groupBy('role_name')->toBase();
    }
    
    public function actorsChanged()
    {
        $this->mount();
    }
    
    public function render()
    {
        return view('livewire.matter.actor-panel');
    }
}
