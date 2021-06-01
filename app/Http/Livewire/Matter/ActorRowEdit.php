<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use App\ActorPivot;

class ActorRowEdit extends Component
{
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

    public function updated()
    {
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
