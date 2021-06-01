<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\ActorPivot;

class ActorRowEdit extends Component
{
    protected $listeners = ['autoCompleted'];
    public ActorPivot $actorPivot;
    public $actor_item;

    protected $rules = [
        'actorPivot.actor_ref' => 'string',
        'actorPivot.date' => 'date',
        'actorPivot.rate' => 'numeric',
        'actorPivot.shared' => 'numeric',
        'actorPivot.display_order' => 'numeric',
    ];

    public function mount()
    {
        $this->actorPivot = ActorPivot::find($this->actor_item->id);
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
            case 'company':
                $this->actorPivot->company_id = $id;
                break;
            case 'role':
                $this->actorPivot->role = $id;
                break;
        }

        $this->updated();
    }
    
    public function updated()
    {
        $this->validate();
        $this->actorPivot->save();
        $this->emitUp('refreshActorCard');
    }

    public function removeActor()
    {
        $this->actorPivot->delete();
        $this->emitUp('refreshActorCard');
    }

    public function render()
    {
        return view('livewire.matter.actor-row-edit');
    }
}
