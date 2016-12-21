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
    
    public function show(Matter $matter)
    {
    	return view('matter.show', compact('matter'));
    }
}
