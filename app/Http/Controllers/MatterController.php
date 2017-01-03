<?php

namespace App\Http\Controllers;

use App\Matter;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Database\Query\Builder;
class MatterController extends Controller {
	/*
	 * public function index(Request $request) {
	 * $get_sort = $request->input ( 'sort' );
	 * $get_dir = $request->input ( 'dir' );
	 * $sort_field = isset ( $get_sort ) ? $get_sort : 'caseref';
	 * $sort_dir = isset ( $get_dir ) ? $get_dir : 'asc';
	 * $page = $request->input ( 'page', 1 );
	 */
	
	/*
	 * $mfs = new Zend_Session_Namespace ( 'matter_filter' );
	 * $mfs->sort_field = $sort_field;
	 * $mfs->sort_dir = $sort_dir;
	 * $mfs->multi_sort = array ();
	 * $mfs->display_style = $this->view->display_style;
	 * $mfs->category_display = $this->view->category_display;
	 */
	
	/*
	 * $matter = New Matter();
	 * $matters = $matter->list ( $sort_field, $sort_dir, [], $request->input ( 'display' ), true );
	 * $matters->appends ( $request->input () )->links (); // Keep URL parameters in the pages
	 *
	 * $matters->category_display = $request->input ( 'display' );
	 * $matters->display_style = $request->input ( 'displaystyle' );
	 * $matters->sort_id = 'caseref';
	 * $matters->sort_dir = 'asc';
	 *
	 * return view('matter.index', compact('matters'));
	 * //return $matters;
	 * }
	 */
	public function index(Request $request) {
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
			// ZF1 $matter->render ( 'index' );
			return view ( 'matter.index', compact ( 'matters' ) );
		}
	}
	public function view(Matter $matter) {
		// $this->authorize('view', $matter);
		// return view('matter.view', compact('matter'));
		return $matter;
	}
}
