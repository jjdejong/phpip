<?php

namespace App\Http\Controllers;

use App\ActorPivot;
use Illuminate\Http\Request;

class ActorPivotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
      $request->validate([
        'matter_id' => 'required|numeric',
        'actor_id'  => 'required|numeric',
        'role'      => 'required'
      ]);

      ActorPivot::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ActorPivot  $actorPivot
     * @return \Illuminate\Http\Response
     */
    public function show(ActorPivot $actorPivot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ActorPivot  $actorPivot
     * @return \Illuminate\Http\Response
     */
    public function edit(ActorPivot $actorPivot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ActorPivot  $actorPivot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActorPivot $actorPivot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ActorPivot  $actorPivot
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActorPivot $actorPivot)
    {
        //
    }
}
