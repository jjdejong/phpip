<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
use App\ActorPivot;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// use App\Http\Controllers\Controller;
// use Illuminate\Database\Query\Builder;

class MatterController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir',
            'tab',
            'include_dead'
        ]);

        $matters = Matter::filter($request->input('sortkey', 'id'), $request->input('sortdir', 'desc'), $filters, $request->display_with, $request->include_dead, true);
        $matters->appends($request->input())->links(); // Keep URL parameters in the paginator links

        return view('matter.index', compact('matters'));
    }

    public function show(Matter $matter)
    {
        $matter->load(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers']);
        return view('matter.show', compact('matter'));
    }

    /**
     * Return a JSON array with info of a matter. For use with API REST.
     * @param  int  $id
     * @return Json
    **/
    public function info($id)
    {
        return Matter::with(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers'])->find($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operation = $request->input('operation', 'new'); // new, clone, child
        $category = [];
        $category_code = $request->input('category');
        if ($operation != 'new') {
            $from_matter = Matter::with('container', 'countryInfo', 'originInfo', 'category', 'type')->find($request->matter_id);
            if ($operation == 'clone') {
                // Generate the next available caseref based on the prefix
                $from_matter->caseref = Matter::where('caseref', 'like', $from_matter->category->ref_prefix . '%')->max('caseref');
                ++$from_matter->caseref;
            }
        } else {
            $from_matter = new Matter; // Create empty matter object to avoid undefined errors in view
            if ($category_code != '') {
                $ref_prefix = \App\Category::find($category_code)['ref_prefix'];
                $category=[
                    'code' => $category_code,
                    'next_caseref' =>  Matter::where('caseref', 'like', $ref_prefix . '%')->max('caseref'),
                    'name' => \App\Category::find($category_code)['category']
                ];
                ++$category['next_caseref'];
            }
        }
        return view('matter.create', compact('from_matter', 'operation', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_code' => 'required',
            'caseref' => 'required',
            'country' => 'required',
            'responsible' => 'required',
            'expire_date' => 'date'
        ]);

        // Unique UID handling
        $matters = Matter::where([
            ['caseref', $request->caseref],
            ['country', $request->country],
            ['category_code', $request->category_code],
            ['origin', $request->origin],
            ['type_code', $request->type_code]
        ]);

        $request->merge([ 'creator' => Auth::user()->login ]);

        $idx = $matters->count();

        if ($idx > 0) {
            $request->merge(['idx' => $idx + 1]);
        }

        $new_matter = Matter::create($request->except(['_token', '_method', 'operation', 'origin_id', 'priority']));

        switch ($request->operation) {
          case 'child':
            $from_matter = Matter::with('priority')->find($request->origin_id);
            // Copy priority claims from original matter
            $new_matter->priority()->createMany($from_matter->priority->toArray());
            $new_matter->container_id = $from_matter->container_id ?? $request->origin_id;
            if ($request->priority) {
                $event = new Event(
                    ['code' => 'PRI', 'alt_matter_id' => $request->origin_id]
                );
            } else {
                $new_matter->parent_id = $request->origin_id;
                $event = new Event(
                    ['code' => 'PFIL', 'alt_matter_id' => $request->origin_id]
                );
            }
            $new_matter->events()->save($event);
            $new_matter->save();
            break;
          case 'clone':
            $from_matter = Matter::with('priority', 'classifiersNative', 'actorPivot')->find($request->origin_id);
            // Copy priority claims from original matter
            $new_matter->priority()->createMany($from_matter->priority->toArray());
            // Copy actors from original matter (cannot use Eloquent relationships because they do not handle unique key constraints)
            $actors = $from_matter->actorPivot;
            $new_matter_id = $new_matter->id;
            $actors->each(function ($item) use ($new_matter_id) {
                $item->matter_id = $new_matter_id;
                $item->id = null;
            });
            ActorPivot::insertIgnore($actors->toArray());
            if ($from_matter->container_id) {
                // Copy shared actors and classifiers from original matter's container
                $actors = $from_matter->container->actorPivot->where('shared', 1);
                $actors->each(function ($item) use ($new_matter_id) {
                    $item->matter_id = $new_matter_id;
                    $item->id = null;
                });
                ActorPivot::insertIgnore($actors->toArray());
                $new_matter->classifiersNative()->createMany($from_matter->container->classifiersNative->toArray());
            } else {
                // Copy classifiers from original matter
                $new_matter->classifiersNative()->createMany($from_matter->classifiersNative->toArray());
            }
            break;
          case 'new':
            $received_event = new Event([
              'code' => 'REC',
              'event_date' => now()
            ]);
            $new_matter->events()->save($received_event);
            break;
        }
        return response()->json(['redirect' => route('matter.show', [$new_matter])]);
    }

    /**
     * Store multiple newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeN(Request $request)
    {
        $this->validate($request, [
            'ncountry' => 'required:array'
        ]);

        $origin_id = $request->origin_id;
        $from_matter = Matter::with('priority', 'filing', 'publication', 'grant', 'classifiersNative')->find($origin_id);

        foreach ($request->ncountry as $country) {
            $request->merge([
              'country' => $country,
              'creator' => Auth::user()->login
            ]);

            $new_matter = Matter::create($request->except(['_token', '_method', 'ncountry', 'origin_id']));

            // Copy classifiers (from original matter's container, or from original matter if there is no container)
            /*** THIS IS NOT NECESSARY - classifiers are necessarily inherited here! ***
            if ($from_matter->container_id) {
                $new_matter->classifiersNative()->createMany($from_matter->container->classifiersNative->toArray());
                $new_matter->container_id = $from_matter->container_id;
            } else {
                $new_matter->classifiersNative()->createMany($from_matter->classifiersNative->toArray());
                $new_matter->container_id = $origin_id;
            } */

            // Copy shared events from original matter
            $new_matter->priority()->createMany($from_matter->priority->toArray());
            $new_matter->filing()->create($from_matter->filing->toArray());
            if ($from_matter->publication()->exists()) {
              $new_matter->publication()->create($from_matter->publication->toArray());
            }
            if ($from_matter->grant()->exists()) {
              $new_matter->grant()->create($from_matter->grant->toArray());
            }

            // Insert "entered" event
            $new_matter->events()->create(["code" => 'ENT', "event_date" => date('Y-m-d')]);

            $new_matter->parent_id = $origin_id;
            $new_matter->save();
        }

        return response()->json(['redirect' => "/matter?Ref=$request->caseref&origin=$from_matter->country"]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function edit(Matter $matter)
    {
        $matterInfo = $matter->with('container', 'parent', 'countryInfo:iso,name', 'originInfo:iso,name', 'category', 'type', 'events')->first();
        if ($matter->events) {
            $cat_edit = 0;
            $country_edit = 0;
        } else {
            $cat_edit = 1;
            $country_edit = 1;
        }
        $cats = \App\Category::all();
        $types = \App\Type::all();
        return view("matter.edit", compact(['matter','cats','types','cat_edit', 'country_edit']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Matter $matter)
    {
        $request->validate([
            'term_adjust' => 'numeric',
            'idx' => 'numeric|nullable',
            'expire_date' => 'date'
        ]);
        $request->merge([ 'updater' => Auth::user()->login ]);
        $matter->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Matter updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Matter $matter)
    {
        $matter->delete();
        return response()->json(['success' => 'Matter deleted']);
    }

    /**
     * Exports Matters list
     * *
     */
    public function export(Request $request)
    {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir',
            'tab'
        ]);

        $matter = new Matter();
        $export = $matter->filter($request->input('sortkey', 'caseref'), $request->input('sortdir', 'asc'), $filters, $request->display_with, false)->toArray();

        $captions = [
            'Omnipat',
            'Country',
            'Cat',
            'Origin',
            'Status',
            'Status date',
            'Client',
            'Client Ref',
            'Applicant',
            'Agent',
            'Agent Ref',
            'Title',
            'Title2',
            'Inventor 1',
            'Filed',
            'FilNo',
            'Published',
            'Pub. No',
            'Granted',
            'Grt No',
            'ID',
            'container_ID',
            'parent_ID',
            'Responsible',
            'Delegate',
            'Dead',
            'Ctnr'
        ];

        $export_csv = fopen('php://memory', 'w');
        fputcsv($export_csv, $captions, ';');
        foreach ($export as $row) {
            fputcsv($export_csv, array_map("utf8_decode", $row), ';');
        }
        rewind($export_csv);
        $filename = 'phpIP-export.csv';

        return response()->stream(
            function () use ($export_csv) {
                fpassthru($export_csv);
            },
            200,
            [ 'Content-Type' => 'application/csv', 'Content-disposition' => 'attachment; filename=' . $filename ]
        );
    }

    public function events(Matter $matter)
    {
        $events = $matter->events->load('info');
        return view('matter.events', compact('events', 'matter'));
    }

    public function tasks(Matter $matter)
    { // All events and their tasks, excepting renewals
        $events = Event::with(['tasks' => function ($query) {
            $query->where('code', '!=', 'REN');
        }, 'info:code,name', 'tasks.info:code,name'])->where('matter_id', $matter->id)
        ->orderBy('event_date')->get();
        return view('matter.tasks', compact('events', 'matter'));
    }

    public function renewals(Matter $matter)
    { // The renewal trigger event and its renewals
        $events = Event::with(['tasks' => function ($query) {
            $query->where('code', 'REN');
        }])->whereHas('tasks', function ($query) {
            $query->where('code', 'REN');
        })->where('matter_id', $matter->id)->get();
        return view('matter.tasks', compact('events', 'matter'));
    }

    public function actors(Matter $matter, $role)
    {
        $role_group = $matter->actors->where('role_code', $role);
        return view('matter.roleActors', compact('role_group', 'matter'));
    }

    public function classifiers(Matter $matter)
    {
        $matter->load(['classifiers']);
        return view('matter.classifiers', compact('matter'));
    }

    public function description(Matter $matter, $lang)
    {
        $description = $matter->getDescription($matter->id, $lang);
        return view('matter.summary', compact('description'));
    }

    /**
    * list Matters for actor by display name, where actors found are registered directly inside or in the related container or parent
    * 
     * @param  string  $dname  value to search in display_name
     * @return Json     list of matters 
    */
    public function filesByActor(string $dname) {
        $dname = urldecode($dname);
        $actorpivot1 = new ActorPivot();
        $actorpivot2 = new ActorPivot();
        $actorpivot3 = new ActorPivot();
        $actor_model = new Actor();
        $matter = new Matter();

        $actor_list = $actor_model->select(['id'])->where('display_name','like',$dname.'%')->get()->toArray();
        $ma1 = $actorpivot1->select(['matter_id'])->whereIn('actor_id', $actor_list)->get()->toArray();
        $ma2 = $actorpivot2->select(['matter_id'])->whereIn('actor_id', $actor_list)->where('shared',1)->get()->toArray();
        $matter_list1 = $matter->select('id')->whereIn('parent_id',$ma2)->get()->toArray();
        $matter_list2 = $matter->select('id')->whereIn('container_id',$ma2)->get()->toArray();
        
        $matter_actor = $matter->select(['id','caseref','suffix'])->whereIn('id',array_merge($ma1, $matter_list1, $matter_list2))->get();
        return $matter_actor;
    }
}
