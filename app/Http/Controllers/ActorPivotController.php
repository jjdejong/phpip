<?php

namespace App\Http\Controllers;

use App\ActorPivot;
use App\Actor;
use Illuminate\Support\Facades\DB;
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

      $addedActor = Actor::find($request->actor_id);

      $request->merge(['display_order' => $max + 1, 'company_id' => $addedActor->company_id]);

      $new_actorPivot = ActorPivot::create($request->except(['_token', '_method']));

      return $new_actorPivot;

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
      return response()->json(['success' => 'Link updated']);
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

      return response()->json(['success' => 'Actor unlinked']);
    }

    /**
        * show Matters where actor is used
        * *
        */
    public function usedIn(int $actor) {
        $actorpivot = new ActorPivot();
        $matter_dependencies = $actorpivot->with('matter','role')->where('actor_id', $actor)->get()->take(50);
        $actor_model = new Actor();
        $other_dependencies = $actor_model->select(array('id', DB::Raw("concat_ws(' ', name, first_name) as Actor"), DB::Raw("(case ".$actor."
           when parent_id then 'Parent'
           when company_id then 'Company'
           when site_id then 'Site'
         end) as Dependency")))->where('parent_id', $actor)->orWhere('company_id', $actor)->orWhere('site_id',$actor)->get()->take(30);
        return view('actor.usedin', compact(['matter_dependencies','other_dependencies']));
	}

}
