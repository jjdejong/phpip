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

      // Fix display order indexes if wrong
      $roleGroup = ActorPivot::where('matter_id', $request->matter_id)->where('role', $request->role);
      $max = $roleGroup->max('display_order');
      $count = $roleGroup->count();
      if ( $count < $max ) {
        $i = 0;
        $actors = $roleGroup->orderBy('display_order')->get();
        foreach ( $actors as $actor ) {
          $i++;
          $actor->display_order = $i;
          $actor->save();
        }
        $max = $i;
      }

      $request->merge(['display_order' => $max + 1]);

      try {
				ActorPivot::create($request->except(['_token', '_method']));
			} catch (Exception $e) {
				report($e);
				return false;
			}
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
    	$actorPivot->update($request->except(['_token', '_method']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ActorPivot  $actorPivot
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActorPivot $actorPivot)
    {
      $matter_id = $actorPivot->matter_id;
      $role = $actorPivot->role;

      $actorPivot->delete();

      // Reorganize remaining items in role
      $actors = ActorPivot::where('matter_id', $matter_id)->where('role', $role)->orderBy('display_order')->get();
      $i = 0;
      foreach ( $actors as $actor ) {
        $i++;
        $actor->display_order = $i;
        $actor->save();
      }
    }
}
