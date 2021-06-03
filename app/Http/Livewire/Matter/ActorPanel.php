<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\Matter;

class ActorPanel extends Component
{
    protected $listeners = ['actorChanged'];
    public $matter_id;
    public $container_id;
    public $actors;
    public $addActive = false;
    
    public function mount()
    {
        $this->actors = Matter::find($this->matter_id)->actors->groupBy('role_name')->toBase();
    }
    
    public function actorChanged($param = null)
    {
        switch ($param) {
            case 'closeActorAdd':
                $this->addActive = false;
                break;
            case 'refreshActorPanel':
                $this->mount();
                break;
        }
    }
    
    public function render()
    {
        return view('livewire.matter.actor-panel');
    }
}
