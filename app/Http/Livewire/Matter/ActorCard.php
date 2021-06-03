<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\Matter;

class ActorCard extends Component
{
    protected $listeners = ['actorChanged'];
    public $matter_id;
    public $container_id;
    public $role_group;
    public $role_code;
    public $role_name;
    public $editActive = false;
    public $addActive = false;

    public function actorChanged($param = null)
    {
        switch ($param) {
            case 'closeActorAdd':
                $this->addActive = false;
                break;
            case 'closeActorEdit':
                $this->editActive = false;
                break;
            default:
                $this->role_group = Matter::find($this->matter_id)->actors()->whereRoleCode($this->role_code)->get();
                if ($this->role_group->count() == 0) {
                    $this->emitUp('actorChanged', 'refreshActorPanel');
                }
        }
    }

    public function render()
    {
        return view('livewire.matter.actor-card');
    }
}
