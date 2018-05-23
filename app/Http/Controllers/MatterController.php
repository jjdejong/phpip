<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Database\Query\Builder;

class MatterController extends Controller {

	public function index (Request $request)
	{
		$category_display = $request->input ( 'display' );
		$get_sort = $request->input ( 'sort' );
		$get_dir = $request->input ( 'dir' );
		$sort_field = isset ( $get_sort ) ? $get_sort : 'caseref';
		$sort_dir = isset ( $get_dir ) ? $get_dir : 'asc';
		$page = $request->input ( 'page', 1 );

		$filters = $request->except ( [
				'display',
				'page',
				'filter',
				'value',
				'sort',
				'dir'
		] );

		$matter = new Matter ();
		$matters = $matter->filter ( $sort_field, $sort_dir, $filters, $category_display, true );
		$matters->appends ( $request->input () )->links (); // Keep URL parameters in the paginator links

		$matters->sort_id = $sort_field;
		$matters->sort_dir = $sort_dir;
		$matters->responsible = @$filters ['responsible'];
		$matters->category_display = $request->input ( 'display' );
		$request->flash (); // Flashes the previous values for storing data typed in forms

		return view ( 'matter.index', compact ( 'matters' ) );
	}

	public function show (Matter $matter)
	{
		$matter->with(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers']);
		return view('matter.show', compact('matter'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$operation = $request->input ( 'operation', 'new' ); // new, clone, child
		if ( $operation != 'new' ) {
			$matter = Matter::find($request->matter_id);
			if ( $operation == 'clone') {
				// Generate the next available caseref based on the prefix
				$matter->caseref = Matter::where('caseref', 'like', $matter->category->ref_prefix . '%')->max('caseref') + 1;
			}
		}

		return view('matter.create', compact('matter', 'operation'));
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
			'responsible' => 'required'
		]);

		// Unique UID handling
		$matters = Matter::where([
			[ 'caseref', $request->caseref ],
			[ 'country', $request->country ],
			[ 'category_code', $request->category_code ],
			[ 'origin', $request->origin ],
			[ 'type_code', $request->type_code ]
		]);

		$idx = $matters->count();

		if ($idx > 0) {
			$request->merge(['idx' => $idx + 1]);
		}

		try {
			$new_matter = Matter::create( $request->except(['_token', '_method', 'operation', 'origin_id', 'origin_container_id', 'priority']) );
		} catch (Exception $e) {
			report($e);
			return false;
		}

		if ( $request->operation != 'new' ) {
			$origin_id = $request->origin_id;
			$from_origin = [ $new_matter->id, $origin_id ];
			// Copy non-shared actors from original matter
			DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
				SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
				FROM matter_actor_lnk
				WHERE matter_id=? AND shared=0", $from_origin);

			// Copy classifiers (from original matter's container)
			DB::statement("INSERT INTO classifier (matter_id, type_code, value, url, value_id, display_order, lnk_matter_id)
				SELECT ?, type_code, value, url, value_id, display_order, lnk_matter_id
				FROM classifier WHERE matter_id=?", [ $new_matter->id, $request->input('origin_container_id', $origin_id) ]);

			// Copy priority claims from original matter
			DB::statement("INSERT INTO event (code, matter_id, event_date, alt_matter_id, detail, notes)
				SELECT 'PRI', ?, event_date, alt_matter_id, detail, notes
				FROM event WHERE matter_id=? AND code='PRI'", $from_origin);

			if ( $request->operation == 'child' ) {
				$new_matter->container_id = $request->input('origin_container_id', $origin_id);
				if ( $request->priority ) {
					DB::table('event')->insert(
						[ 'matter_id' => $new_matter->id, 'code' => 'PRI', 'alt_matter_id' => $origin_id ]
					);
				} else {
					$new_matter->parent_id = $origin_id;
					DB::table('event')->insert(
						[ 'matter_id' => $new_matter->id, 'code' => 'PFIL', 'alt_matter_id' => $origin_id ]
					);
				}
				$new_matter->save();
			}

			if ( $request->operation == 'clone' ) {
				// Copy shared actors from original matter or its container
				if ( $request->has('origin_container_id') ) {
					$from_matter = [ $new_matter->id, $request->origin_container_id ];
				} else {
					$from_matter = $from_origin;
				}
				DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
					SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
					FROM matter_actor_lnk
					WHERE matter_id=? AND shared=1", $from_matter);
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
	public function storeN(Request $request)
	{
		$this->validate($request, [
			'ncountry' => 'required:array'
		]);


		foreach ($request->ncountry as $country) {

			$request->merge(['country' => $country]);

			try {
				$new_matter = Matter::create( $request->except(['_token', '_method', 'ncountry', 'origin_id', 'origin_container_id']) );
			} catch (Exception $e) {
				report($e);
				return false;
			}

			$origin_id = $request->origin_id;
			$from_origin = [ $new_matter->id, $origin_id ];
			// Copy non-shared actors from original matter
			DB::statement("INSERT IGNORE INTO matter_actor_lnk (matter_id, actor_id, display_order, role, shared, actor_ref, company_id, rate, date)
				SELECT ?, actor_id, display_order, role, shared, actor_ref, company_id, rate, date
				FROM matter_actor_lnk
				WHERE matter_id=? AND shared=0", $from_origin);

			// Copy classifiers (from original matter's container, or from original matter if there is no container)
			DB::statement("INSERT INTO classifier (matter_id, type_code, value, url, value_id, display_order, lnk_matter_id)
				SELECT ?, type_code, value, url, value_id, display_order, lnk_matter_id
				FROM classifier WHERE matter_id=?", [ $new_matter->id, $request->input('origin_container_id', $origin_id) ]);

			// Copy priority claims from original matter
			DB::statement("INSERT INTO event (code, matter_id, event_date, alt_matter_id, detail, notes)
				SELECT 'PRI', ?, event_date, alt_matter_id, detail, notes
				FROM event WHERE matter_id=? AND code='PRI'", $from_origin);

			// Copy filing from original matter
			DB::statement("INSERT INTO event (code, matter_id, event_date, detail, notes)
				SELECT 'FIL', ?, event_date, detail, notes
				FROM event WHERE matter_id=? AND code='FIL'", $from_origin);

			// Insert "entered" event
			$entered = new Event;
			$entered->matter_id = $new_matter->id;
			$entered->code = 'ENT';
			$entered->event_date = date('Y-m-d');
			$entered->save();

			$new_matter->parent_id = $origin_id;
			$new_matter->container_id = $request->input('origin_container_id', $origin_id);
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
	public function edit(Matter $matter)
	{
		//
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
		$matter->update($request->except(['_token', '_method']));
	}

	/**
	 * Exports Matters list
	 * *
	 */
	public function export (Request $request)
	{
		$category_display = $request->input ( 'display' );
		$get_sort = $request->input ( 'sort' );
		$get_dir = $request->input ( 'dir' );
		$sort_field = isset ( $get_sort ) ? $get_sort : 'caseref';
		$sort_dir = isset ( $get_dir ) ? $get_dir : 'asc';

		$filters = $request->except ( [
				'display',
				'page',
				'filter',
				'value',
				'sort',
				'dir'
		] );

		$matter = new Matter ();
		$export = $matter->filter ( $sort_field, $sort_dir, $filters, $category_display, false )->toArray ();

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

		$export_csv = fopen ( 'php://memory', 'w' );
		fputcsv ( $export_csv, $captions, ';' );
		foreach ( $export as $row ) {
			fputcsv ( $export_csv, array_map ( "utf8_decode", $row ), ';' );
		}
		rewind ( $export_csv );
		$filename = 'phpIP-export.csv';

		return response ()->stream (
			function () use ( $export_csv ) { fpassthru ( $export_csv ); },
			200,
			[ 'Content-Type' => 'application/csv', 'Content-disposition' => 'attachment; filename=' . $filename ]
		);
	}

	public function events(Matter $matter)
	{
		$events = $matter->events->load('info');
		/*= Event::with('info')
		->where('matter_id', $matter->id)
		->orderBy('event_date')->get();*/
		return view('matter.events', compact('events', 'matter'));
	}

	public function tasks(Matter $matter) // All events and their tasks, excepting renewals
	{
		$events = Event::with(['tasks' => function($query) {
			$query->where('code', '!=', 'REN');
		}])->where('matter_id', $matter->id)
		->orderBy('event_date')->get();
		return view('matter.tasks', compact('events', 'matter'));
	}

	public function renewals(Matter $matter) // The renewal trigger event and its renewals
	{
		$events = Event::with(['tasks' => function($query) {
			$query->where('code', 'REN');
		}])->whereHas('tasks', function($query) {
			$query->where('code', 'REN');
		})->where('matter_id', $matter->id)->get();
		return view('matter.tasks', compact('events', 'matter'));
	}

	public function actors(Matter $matter, $role)
	{
		$role_group = $matter->actors->where('role_code', $role);
		return view('matter.roleActors', compact('role_group', 'matter'));
	}
}
