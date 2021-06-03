<?php

namespace App\Http\Livewire\Matter;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
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
        'actorPivot.shared' => 'boolean',
        'actorPivot.display_order' => 'numeric',
        'actorPivot.updater' => 'string',
    ];

    public function mount()
    {
        $this->actorPivot = ActorPivot::find($this->actor_item->id);
        //dd($this->actorPivot);
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

        $this->updated($name = null);
    }
    
    public function updated($name)
    {
        $this->actorPivot->updater = Auth::user()->login;
        $this->validate();
        $this->actorPivot->save();
        if ($name == 'actorPivot.display_order') {
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
        }
        $this->emitUp('actorChanged');
    }

    public function removeActor()
    {
        $this->actorPivot->delete();
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
        $this->emitUp('actorChanged');
    }

    public function render()
    {
        return view('livewire.matter.actor-row-edit');
    }
}
