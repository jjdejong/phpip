<?php

namespace App\Http\Controllers;

use DB;
//use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;

class MatterController extends Controller
{
    public function index()
    {
    	$matters = DB::table('matter')->get();
    	return view('matter.index', compact('matters'));
    }
}
