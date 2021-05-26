<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\ActorPivot;
use App\Role;
use App\Actor;

class AddActor extends Component
{
    protected $listeners = ['autoCompleted'];
    public $container_id;
    public $matter_id;
    public ActorPivot $actorPivot;
    public $role_name = 'Actor';

    protected $rules = [
        'actorPivot.matter_id' => 'required|numeric',
        'actorPivot.actor_id' => 'required|numeric',
        'actorPivot.role' => 'required|string',
        'actorPivot.shared' => 'numeric',
        'actorPivot.date' => 'date',
        'actorPivot.actor_ref' => 'string',
    ];

    public function mount()
    {
        $this->actorPivot = new ActorPivot;
        $this->actorPivot->shared = 1;
        $this->actorPivot->matter_id = $this->matter_id;
        $this->actorPivot->date = Now();
        $this->actorPivot->creator = Auth::user()->login;
    }
    
    public function autoCompleted($id, $placeholder)
    {
        switch ($placeholder) {
            case 'Name':
                $this->actorPivot->actor_id = $id;
                break;
            case 'Role':
                $this->actorPivot->role = $id;
                $this->actorPivot->shared = Role::find($id)->shareable;
                // Update the popup header with the selected role name
                $this->role_name = Role::find($id)->name;
                break;
        }
    }

    public function submit()
    {
        if ($this->actorPivot->shared == 1) {
            $this->actorPivot->matter_id = $this->container_id ?? $this->matter_id;
        }

        $fromActorCard = 0;
        if (!$this->actorPivot->role) {
            $this->actorPivot->role = Role::whereName($this->role_name)->first()->code;
            $fromActorCard = 1;
        }
        
        $this->validate();

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

        $addedActor = Actor::find($this->actorPivot->actor_id);

        $this->actorPivot->display_order = $count + 1;
        $this->actorPivot->company_id = $addedActor->company_id;
        $this->actorPivot->save();
        
        if ($fromActorCard) {
            $this->emit('actorAdded');
        } else {
            $this->reset('role_name');
            $this->emit('actorsChanged');
        }

        $this->emitTo('actor-autocomplete', 'resetAutoComplete');
    }
    
    public function render()
    {
        return view('livewire.add-actor');
    }
}
