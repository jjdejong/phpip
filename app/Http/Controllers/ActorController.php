<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for managing actors (individuals and organizations).
 *
 * Handles CRUD operations for actors such as clients, agents, inventors,
 * applicants, and other parties involved in IP matters.
 */
class ActorController extends Controller
{
    /**
     * Display a paginated list of actors with optional filtering.
     *
     * @param Request $request The HTTP request containing filter parameters.
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse The view or JSON response with filtered actors.
     */
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

    /**
     * Show the form for creating a new actor.
     *
     * @return \Illuminate\Http\Response The view for creating a new actor.
     */
    public function create()
    {
        Gate::authorize('readwrite');
        $actor = new Actor;
        $actorComments = $actor->getTableComments();

        return view('actor.create', compact('actorComments'));
    }

    /**
     * Store a new actor in the database.
     *
     * @param Request $request The HTTP request containing actor data.
     * @return Actor The newly created actor model.
     */
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

    /**
     * Display detailed information for a specific actor.
     *
     * @param Actor $actor The actor to display.
     * @return \Illuminate\Http\Response The view with actor details.
     */
    public function show(Actor $actor)
    {
        Gate::authorize('readonly');
        $actorInfo = $actor->load(['company:id,name', 'parent:id,name', 'site:id,name', 'droleInfo', 'countryInfo:iso,name', 'country_mailingInfo:iso,name', 'country_billingInfo:iso,name', 'nationalityInfo:iso,name']);
        $actorComments = $actor->getTableComments();

        return view('actor.show', compact('actorInfo', 'actorComments'));
    }

    /**
     * Show the form for editing an actor.
     *
     * @param Actor $actor The actor to edit.
     * @return void Not implemented.
     */
    public function edit(Actor $actor)
    {
        //
    }

    /**
     * Update an actor in the database.
     *
     * @param Request $request The HTTP request containing updated actor data.
     * @param Actor $actor The actor to update.
     * @return Actor The updated actor model.
     */
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

    /**
     * Remove an actor from the database.
     *
     * @param Actor $actor The actor to delete.
     * @return Actor The deleted actor model.
     */
    public function destroy(Actor $actor)
    {
        Gate::authorize('readwrite');
        $actor->delete();

        return $actor;
    }
}
