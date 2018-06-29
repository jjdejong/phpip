<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// use App\Http\Controllers\Controller;
// use Illuminate\Database\Query\Builder;

class MatterController extends Controller 
{

    public function index(Request $request) {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir'
        ]);

        $matters = Matter::filter($request->input('sortkey', 'caseref'), $request->input('sortdir', 'asc'), $filters, $request->display_with, true);
        $matters->appends($request->input())->links(); // Keep URL parameters in the paginator links

        return view('matter.index', compact('matters'));
    }

    public function show(Matter $matter) {
        $matter->with(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers']);
        return view('matter.show', compact('matter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $operation = $request->input('operation', 'new'); // new, clone, child
        if ($operation != 'new') {
            $from_matter = Matter::with('container', 'countryInfo', 'originInfo', 'category', 'type')->find($request->matter_id);
            if ($operation == 'clone') {
                // Generate the next available caseref based on the prefix
                $from_matter->caseref = Matter::where('caseref', 'like', $from_matter->category->ref_prefix . '%')->max('caseref') + 1;
            }
        } else {
            $from_matter = collect(new Matter); // Create empty matter object
        }

        return view('matter.create', compact('from_matter', 'operation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'category_code' => 'required',
            'caseref' => 'required',
            'country' => 'required',
            'responsible' => 'required'
        ]);

        // Unique UID handling
        $matters = Matter::where([
            ['caseref', $request->caseref],
            ['country', $request->country],
            ['category_code', $request->category_code],
            ['origin', $request->origin],
            ['type_code', $request->type_code]
        ]);

        $idx = $matters->count();

        if ($idx > 0) {
            $request->merge(['idx' => $idx + 1]);
        }

        try {
            $new_matter = Matter::create($request->except(['_token', '_method', 'operation', 'origin_id', 'priority']));
        } catch (Exception $e) {
            report($e);
            return false;
        }

        if ($request->operation != 'new') {
            $origin_id = $request->origin_id;

            $from_matter = Matter::with('priority', 'classifiersNative')->find($origin_id);
            //$container = $from_matter->container;
            // Copy actors from original matter (cannot use Eloquent relationships because they do not handle unique key constraints)
            // $new_matter->actorsNative()->createMany($from_matter->actorsNative->toArray());

            DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
                            SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
                            FROM matter_actor_lnk
                            WHERE matter_id=?", [$new_matter->id, $origin_id]);

            // Copy priority claims from original matter
            $new_matter->priority()->createMany($from_matter->priority->toArray());

            if ($request->operation == 'child') {
                $new_matter->container_id = $request->input('origin_container_id', $origin_id);
                if ($request->priority) {
                    $event = new Event(
                        ['code' => 'PRI', 'alt_matter_id' => $origin_id]
                    );
                } else {
                    $new_matter->parent_id = $origin_id;
                    $event = new Event(
                        ['code' => 'PFIL', 'alt_matter_id' => $origin_id]
                    );
                }
                $new_matter->events()->save($event);
                $new_matter->save();
            }

            if ($request->operation == 'clone') {
                if ($from_matter->has('container')) {
                    // Copy shared actors and classifiers from original matter's container
                    DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
                                    SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
                                    FROM matter_actor_lnk
                                    WHERE matter_id=? AND shared=1", [$new_matter->id, $from_matter->container_id]);
                    $new_matter->classifiersNative()->createMany($from_matter->container->classifiersNative->toArray());
                } else {
                    $new_matter->classifiersNative()->createMany($from_matter->classifiersNative->toArray());
                }
            }
        }

        return route('matter.show', [$new_matter]);
    }

    /**
     * Store multiple newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeN(Request $request) {
        $this->validate($request, [
            'ncountry' => 'required:array'
        ]);

        $origin_id = $request->origin_id;
        $from_matter = Matter::with('priority', 'filing', 'classifiersNative')->find($origin_id);

        foreach ($request->ncountry as $country) {

            $request->merge(['country' => $country]);

            try {
                $new_matter = Matter::create($request->except(['_token', '_method', 'ncountry', 'origin_id']));
            } catch (Exception $e) {
                report($e);
                return false;
            }

            // Copy actors from original matter
            DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
                            SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
                            FROM matter_actor_lnk
                            WHERE matter_id=?", [$new_matter->id, $origin_id]);

            // Copy classifiers (from original matter's container, or from original matter if there is no container)
            if ($from_matter->has('container')) {
                $new_matter->classifiersNative()->createMany($from_matter->container->classifiersNative->toArray());
                $new_matter->container_id = $from_matter->container_id;
            } else {
                $new_matter->classifiersNative()->createMany($from_matter->classifiersNative->toArray());
                $new_matter->container_id = $origin_id;
            }

            // Copy priority claims from original matter
            $new_matter->priority()->createMany($from_matter->priority->toArray());

            // Copy filing from original matter
            $new_matter->filing()->create($from_matter->filing->toArray());

            // Insert "entered" event
            $new_matter->events()->create(["code" => 'ENT', "event_date" => date('Y-m-d')]);

            $new_matter->parent_id = $origin_id;
            $new_matter->save();
        }

        return "/matter?Ref=$request->caseref";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function edit(Matter $matter) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Matter $matter) {
        $matter->update($request->except(['_token', '_method']));
    }

    /**
     * Exports Matters list
     * *
     */
    public function export(Request $request) {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir'
        ]);

        $matter = new Matter ();
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

        return response ()->stream (
            function () use ( $export_csv ) { fpassthru ( $export_csv ); },
            200,
            [ 'Content-Type' => 'application/csv', 'Content-disposition' => 'attachment; filename=' . $filename ]
        );
    }

    public function events(Matter $matter) {
        $events = $matter->events->load('info');
        /* = Event::with('info')
          ->where('matter_id', $matter->id)
          ->orderBy('event_date')->get(); */
        return view('matter.events', compact('events', 'matter'));
    }

    public function tasks(Matter $matter) { // All events and their tasks, excepting renewals
        $events = Event::with(['tasks' => function($query) {
            $query->where('code', '!=', 'REN');
        }])->where('matter_id', $matter->id)
        ->orderBy('event_date')->get();
        return view('matter.tasks', compact('events', 'matter'));
    }

    public function renewals(Matter $matter) { // The renewal trigger event and its renewals
        $events = Event::with(['tasks' => function($query) {
            $query->where('code', 'REN');
        }])->whereHas('tasks', function($query) {
            $query->where('code', 'REN');
        })->where('matter_id', $matter->id)->get();
        return view('matter.tasks', compact('events', 'matter'));
    }

    public function actors(Matter $matter, $role) {
        $role_group = $matter->actors->where('role_code', $role);
        return view('matter.roleActors', compact('role_group', 'matter'));
    }

}
