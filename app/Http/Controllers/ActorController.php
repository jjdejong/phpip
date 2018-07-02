<?php

namespace App\Http\Controllers;

use App\Actor;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use Response;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $Name = $request->input('Name');
        $Phy_person = $request->input('phy_person');
        $actor = new Actor;
        if (!is_null($Name)) {
            $actor = $actor->where('name', 'like', $Name . '%');
        }
        if (!is_null($Phy_person)) {
            $actor = $actor->where('phy_person', $Phy_person);
        }
        $actorslist = $actor->with('company')->orderby('name')->get();
        return view('actor.index', compact('actorslist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $table = new Actor;
        //TODO getTableComments is the same as in Rule.php. To render common
        $tableComments = $table->getTableComments('actor');
        return view('actor.create', compact('tableComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:100'
        ]);
        try {
            $new_actor = Actor::create($request->except(['_token', '_method']));
            return Response::json($new_actor);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function show(Actor $actor) {
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
    public function edit(Actor $actor) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actor $actor) {

        $actor->update($request->except(['_token', '_method']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $actor = new Actor;
        $actor->destroy($id);
    }

}
