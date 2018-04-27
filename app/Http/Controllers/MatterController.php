<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
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
		//dd($request->input());

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
		$operation = $request->input ( 'operation', 'new' ); // new, clone, child or national
		if ( $operation != 'new' ) {
			$matter = Matter::find($request->input ( 'matter_id' ));
			if ( $operation == 'clone') {
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
			'caseref' => 'required',
			'country' => 'required'
		]);

		Matter::create($request->except(['_token', '_method']));
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
