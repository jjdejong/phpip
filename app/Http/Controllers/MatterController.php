<?php

namespace App\Http\Controllers;

use App\Matter;
//use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;

class MatterController extends Controller
{
    public function index()
    {
    	$matters = Matter::take(100)->get();
    	return view('matter.index', compact('matters'));
    }
    
    public function show($id)
    {
    	$matter = Matter::find($id);
    	return $matter;
    }
}
