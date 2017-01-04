<?php

namespace App\Http\Controllers;

use App\Matter;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Database\Query\Builder;

class MatterController extends Controller {

	public function index (Request $request) {
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
				'dir',
				'display_style' 
		] );
		// dd($request_parameters);
		
		/*
		 * $mfs = new Zend_Session_Namespace ( 'matter_filter' );
		 * $mfs->sort_field = $sort_field;
		 * $mfs->sort_dir = $sort_dir;
		 * $mfs->multi_sort = $request_parameters;
		 */
		
		$matter = new Matter ();
		$matters = $matter->list ( $sort_field, $sort_dir, $filters, $category_display, true );
		$matters->appends ( $request->input () )->links (); // Keep URL parameters in the pages
		
		$matters->sort_id = $sort_field;
		$matters->sort_dir = $sort_dir;
		$matters->responsible = @$filters ['responsible'];
		$matters->category_display = $request->input ( 'display' );
		$matters->display_style = $request->input ( 'display_style' );
		
		if (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
			// Don't know what this is for...
			// ZF1 $this->_helper->layout->disableLayout ();
			return $matters;
		} else {
			$matters->filters = $filters;
			$request->flash ();
			// ZF1 $matter->render ( 'index' );
			return view ( 'matter.index', compact ( 'matters' ) );
		}
	}
	
	public function view (Matter $matter) {
		// $this->authorize('view', $matter);
		// return view('matter.view', compact('matter'));
		return $matter;
	}
	
	/**
	 * Exports Matters list
	 * *
	 */
	public function export (Request $request) {
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
				'dir',
				'display_style'
		] );
		//dd($request);
	
		$matter = new Matter ();
		$export = $matter->list( $sort_field, $sort_dir, $filters, $category_display, false )->toArray ();
	
		$captions = array (
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
		);
		
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
}
