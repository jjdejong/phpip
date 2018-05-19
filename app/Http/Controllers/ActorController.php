<?php

namespace App\Http\Controllers;

use App\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Name = $request->input ( 'Name' );
        $Phy_person = $request->input ( 'phy_person' );
        $actor = new Actor ;
        $actorslist = $actor->actorsList($Name, $Phy_person);
        return view('tables.actorlist', compact('actorslist') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'name' => 'required'
    	]);
    	$input = $request->all();
    	$to_retain = ['_token', '_method'];
    	if($validator->passes()){
			foreach ($input as $i =>$value) {				
				if (strpos($i, '_new')) {
					array_push($to_retain,$i);
				}
			}
			
			Actor::create($request->except($to_retain));
			return Response::json(['success' => '1']);
		}
		return Response::json(['errors' => $validator->errors()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function show($n)
    {
		$actor = new Actor ;
        $actorInfo = $actor->getActorInfo($n);
        //dd($actorInfo);
        $actorComments = $actor->getTableComments('actor');
        return view('tables.actorinfo', compact('actorInfo', 'actorComments') );
    }

    /**
     * Display the content of modal view to add an actor.
     */
    public function addShow()

    {
        $actor = new Actor ;
        //TODO getTableComments is the same as in Rule.php. To render common
        $tableComments = $actor->getTableComments('actor');
        return view('tables.actoradd',compact('tableComments'));
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
   	    	
    	$actor->update($request->except(['_token', '_method']));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function delete(Actor $actor)
    {
    	$actor->delete();
    }

}
