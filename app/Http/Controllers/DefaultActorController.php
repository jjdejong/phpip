<?php

namespace App\Http\Controllers;

use App\Models\DefaultActor;
use Illuminate\Http\Request;

/**
 * Manages default actor assignments for matters.
 *
 * Defines which actors should be automatically assigned to new matters
 * based on country, category, and client. Streamlines matter creation
 * by pre-populating common actor roles.
 */
class DefaultActorController extends Controller
{
    /**
     * Display a list of default actors with filtering.
     *
     * @param Request $request Filter parameters including Actor, Role, Country, Category, Client
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $Actor = $request->input('Actor');
        $Role = $request->input('Role');
        $Country = $request->input('Country');
        $Category = $request->input('Category');
        $Client = $request->input('Client');
        $default_actor = new DefaultActor;

        if (! is_null($Actor)) {
            $default_actor = $default_actor->whereHas('actor', function ($q) use ($Actor) {
                $q->where('name', 'like', $Actor.'%');
            });
        }
        if (! is_null($Role)) {
            $default_actor = $default_actor->whereHas('roleInfo', function ($q) use ($Role) {
                $q->where('name', 'like', $Role.'%');
            });
        }
        if (! is_null($Country)) {
            $default_actor = $default_actor->whereHas('country', function ($q) use ($Country) {
                $q->where('name', 'like', $Country.'%');
            });
        }
        if (! is_null($Category)) {
            $default_actor = $default_actor->whereHas('category', function ($q) use ($Category) {
                $q->where('category', 'like', $Category.'%');
            });
        }
        if (! is_null($Client)) {
            $default_actor = $default_actor->whereHas('client', function ($q) use ($Client) {
                $q->where('name', 'like', $Client.'%');
            });
        }
        $default_actors = $default_actor->with(['roleInfo:code,name', 'actor:id,name', 'client:id,name', 'category:code,category', 'country:iso,name'])->get();

        if ($request->wantsJson()) {
            return response()->json($default_actors);
        }

        return view('default_actor.index', compact('default_actors'));
    }

    /**
     * Show the form for creating a new default actor.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new DefaultActor;
        $tableComments = $table->getTableComments();

        return view('default_actor.create', compact('tableComments'));
    }

    /**
     * Store a newly created default actor.
     *
     * @param Request $request Default actor data including actor_id and role
     * @return DefaultActor The created default actor
     */
    public function store(Request $request)
    {
        $request->validate([
            'actor_id' => 'required',
            'role' => 'required',
        ]);

        return DefaultActor::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified default actor.
     *
     * @param DefaultActor $default_actor The default actor to display
     * @return \Illuminate\Http\Response
     */
    public function show(DefaultActor $default_actor)
    {
        $tableComments = $default_actor->getTableComments();
        $default_actor->with(['roleInfo:code,name', 'actor:id,name', 'client:id,name', 'category:code,category', 'country:iso,name'])->get();

        return view('default_actor.show', compact('default_actor', 'tableComments'));
    }

    /**
     * Update the specified default actor.
     *
     * @param Request $request Updated default actor data
     * @param DefaultActor $default_actor The default actor to update
     * @return DefaultActor The updated default actor
     */
    public function update(Request $request, DefaultActor $default_actor)
    {
        $request->validate([
            'actor_id' => 'sometimes|required',
            'role' => 'sometimes|required',
        ]);
        $default_actor->update($request->except(['_token', '_method']));

        return $default_actor;
    }

    /**
     * Remove the specified default actor from storage.
     *
     * @param DefaultActor $default_actor The default actor to delete
     * @return DefaultActor The deleted default actor
     */
    public function destroy(DefaultActor $default_actor)
    {
        $default_actor->delete();

        return $default_actor;
    }
}
