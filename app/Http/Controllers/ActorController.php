<?php

namespace App\Http\Controllers;

use App\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Actor::class);
        $actor = new Actor;
        if ($request->filled('Name')) {
            $actor = $actor->where('name', 'like', $request->Name . '%');
        }
        switch ($request->selector) {
            case 'phy_p':
                $actor = $actor->where('phy_person', 1);
                break;
            case 'leg_p':
                $actor = $actor->where('phy_person', 0);
                break;
            case 'warn':
                $actor = $actor->where('warn', 1);
                break;
        }
        $actorslist = $actor->with('company')->orderby('name')->paginate(21);
        $actorslist->appends($request->input())->links();
        return view('actor.index', compact('actorslist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Actor::class);
        $table = new Actor;
        //TODO getTableComments is the same as in Rule.php. To render common
        $actorComments = $table->getTableComments('actor');
        return view('actor.create', compact('actorComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Actor::class);
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'email|nullable'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return Actor::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function show(Actor $actor)
    {
        $this->authorize('view', $actor);
        $actorInfo = $actor->load(['company:id,name', 'parent:id,name', 'site:id,name', 'droleInfo', 'countryInfo:iso,name', 'country_mailingInfo:iso,name', 'country_billingInfo:iso,name', 'nationalityInfo:iso,name']);
        $actorComments = $actor->getTableComments('actor');
        return view('actor.show', compact('actorInfo', 'actorComments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function edit(Actor $actor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actor $actor)
    {
        $this->authorize('update', $actor);
        $request->validate([
            'email' => 'email|nullable',
            'ren_discount' => 'numeric'
        ]);
        $request->merge([ 'updater' => Auth::user()->login ]);
        $actor->update($request->except(['_token', '_method']));
        return $actor;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actor $actor)
    {
        $this->authorize('delete', $actor);
        $actor->delete();
        return $actor;
    }
}
