<?php

namespace App\Http\Controllers;

use App\Matter;
//use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;

class MatterController extends Controller
{
    public function index()
    {
    	$matters = Matter::where('category_code', 'PAT')
    	->orderBy('caseref')
    	->orderBy('container_ID')
    	->orderBy('origin')
    	->orderBy('country')
    	->paginate(25);
    	
    	return view('matter.index', compact('matters'));
    }
    
    public function view(Matter $matter)
    {
    	//$this->authorize('view', $matter);
    	return view('matter.view', compact('matter'));
    }
}
