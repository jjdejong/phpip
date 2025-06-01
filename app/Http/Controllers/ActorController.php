<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ActorController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('readonly');
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

        $query = $actor->with('company')->orderby('name');

        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        $actorslist = $query->paginate(21);
        $actorslist->appends($request->input())->links();

        return view('actor.index', compact('actorslist'));
    }

    public function create()
    {
        Gate::authorize('readwrite');
        $actor = new Actor;
        $actorComments = $actor->getTableComments();

        return view('actor.create', compact('actorComments'));
    }

    public function store(Request $request)
    {
        Gate::authorize('readwrite');
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'email|nullable',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return Actor::create($request->except(['_token', '_method']));
    }

    public function show(Actor $actor)
    {
        Gate::authorize('readonly');
        $actorInfo = $actor->load(['company:id,name', 'parent:id,name', 'site:id,name', 'droleInfo', 'countryInfo:iso,name', 'country_mailingInfo:iso,name', 'country_billingInfo:iso,name', 'nationalityInfo:iso,name']);
        $actorComments = $actor->getTableComments();

        return view('actor.show', compact('actorInfo', 'actorComments'));
    }

    public function edit(Actor $actor)
    {
        //
    }

    public function update(Request $request, Actor $actor)
    {
        Gate::authorize('readwrite');
        $request->validate([
            'email' => 'email|nullable',
            'ren_discount' => 'numeric',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        $actor->update($request->except(['_token', '_method']));

        return $actor;
    }

    public function destroy(Actor $actor)
    {
        Gate::authorize('readwrite');
        $actor->delete();

        return $actor;
    }
}
