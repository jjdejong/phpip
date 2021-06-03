<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\ActorPivot;

class ActorAdd extends Component
{
    protected $listeners = ['autoCompleted'];
    public $container_id;
    public $matter_id;
    public ActorPivot $actorPivot;
    public $role_name = 'Actor';
    public $role_code;
    public $role_shareable = 0;

    protected $rules = [
        'actorPivot.matter_id' => 'required|numeric',
        'actorPivot.actor_id' => 'required|numeric',
        'actorPivot.role' => 'required|string',
        'actorPivot.shared' => 'numeric',
        'actorPivot.company_id' => 'numeric',
        'actorPivot.date' => 'date',
        'actorPivot.actor_ref' => 'string',
        'actorPivot.creator' => 'string',
        'actorPivot.display_order' => 'numeric',
    ];

    public function mount()
    {
        $this->actorPivot = new ActorPivot;
        $this->actorPivot->shared = $this->role_shareable;
        $this->actorPivot->matter_id = $this->matter_id;
        $this->actorPivot->role = $this->role_code;
        $this->actorPivot->date = Now();
    }
    
    public function autoCompleted($id, $name, $extra, $source)
    {
        switch ($source) {
            case 'actor':
                $this->actorPivot->actor_id = $id;
                if ($extra) {
                    $this->actorPivot->company_id = $extra;
                }
                break;
            case 'role':
                $this->actorPivot->role = $id;
                $this->actorPivot->shared = $extra;
                // Update the popup header with the selected role name
                if ($name) {
                    $this->role_name = $name;
                }
                break;
        }
    }

    public function submit()
    {
        if ($this->actorPivot->shared == 1) {
            $this->actorPivot->matter_id = $this->container_id ?? $this->matter_id;
        }

        // Fix display order indexes if wrong
        $roleGroup = ActorPivot::where([['matter_id', $this->actorPivot->matter_id], ['role', $this->actorPivot->role]]);
        $max = $roleGroup->max('display_order');
        $count = $roleGroup->count();
        if ($count != $max) {
            $actors = $roleGroup->orderBy('display_order')->get();
            foreach ($actors as $index => $actor) {
                $actor->display_order = $index + 1;
                $actor->save();
            }
        }

        $this->actorPivot->display_order = $count + 1;
        $this->actorPivot->creator = Auth::user()->login;

        $this->validate();
        $this->actorPivot->save();
        
        $this->mount();
        if ($this->role_code) {
            $this->emitUp('actorChanged');
        } else {
            $this->reset(['role_name', 'role_shareable']);
            $this->emit('actorChanged', 'refreshActorPanel');
        }
    }
    
    public function render()
    {
        return view('livewire.matter.actor-add');
    }
}
