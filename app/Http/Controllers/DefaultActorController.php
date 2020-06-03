<?php

namespace App\Http\Controllers;

use App\DefaultActor;
use App\Actor;
use Illuminate\Http\Request;

class DefaultActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Actor  = $request->input('Actor');
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
        $default_actors = $default_actor->with(['roleInfo:code,name','actor:id,name','client:id,name','category:code,category','country:iso,name'])->get();
        return view('default_actor.index', compact('default_actors'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('default_actor');
        return view('default_actor.create', compact('tableComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'actor_id' => 'required',
            'role' => 'required'
        ]);
        return DefaultActor::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $default_actor
     * @return \Illuminate\Http\Response
     */
    public function show(DefaultActor $default_actor)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('default_actor');
        $default_actor->with(['roleInfo:code,name', 'actor:id,name', 'client:id,name', 'category:code,category', 'country:iso,name'])->get();
        return view('default_actor.show', compact('default_actor', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DefaultActor $default_actor)
    {
        $request->validate([
            'actor_id' => 'sometimes|required',
            'role' => 'sometimes|required'
        ]);
        $default_actor->update($request->except(['_token', '_method']));
        return $default_actor;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  DefaultActor $default_actor
     * @return \Illuminate\Http\Response
     */
    public function destroy(DefaultActor $default_actor)
    {
        $default_actor->delete();
        return $default_actor;
    }
}
