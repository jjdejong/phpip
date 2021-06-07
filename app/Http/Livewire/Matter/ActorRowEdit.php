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
    public $container_id;

    protected $rules = [
        'actorPivot.actor_id' => 'required|numeric',
        'actorPivot.actor_ref' => 'string',
        'actorPivot.date' => 'date',
        'actorPivot.rate' => 'numeric',
        'actorPivot.shared' => 'boolean',
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
                if ($id) {
                    $this->actorPivot->actor_id = $id;
                }
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

        $this->updated($source);
    }
    
    public function updated($name = null, $value = null)
    {
        $this->actorPivot->updater = Auth::user()->login;
        if ($name == 'actorPivot.shared' &&
            $this->container_id &&
            $this->container_id != $this->actor_item->matter_id &&
            $value == 1) {
                $this->actorPivot->matter_id = $this->container_id;
        }
        $this->validate();
        $this->actorPivot->save();
        if ($name == 'actorPivot.display_order') {
            $this->fixDisplayOrder();
        }
        $this->emitUp('actorChanged');
    }

    public function removeActor()
    {
        $this->actorPivot->delete();
        $this->fixDisplayOrder();
        $this->emitUp('actorChanged');
    }

    private function fixDisplayOrder()
    {
        $roleGroup = ActorPivot::where([['matter_id', $this->actorPivot->matter_id], ['role', $this->actorPivot->role]]);
        $first = $roleGroup->min('display_order');
        $last = $roleGroup->max('display_order');
        $n = $roleGroup->count();
        if ($first + $last != 1 + $n) {
            $actors = $roleGroup->orderBy('display_order')->get();
            foreach ($actors as $index => $actor) {
                $actor->display_order = $index + 1;
                $actor->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.matter.actor-row-edit');
    }
}
