<?php

namespace App\Http\Controllers;

use App\Classifier;
use Illuminate\Http\Request;

class ClassifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    		'matter_id' => 'required',
    		'type' => 'required',
    		'type_code' => 'required',
    		'value' => 'required'
    	]);
    	
    	Classifier::create($request->except(['_token', '_method', 'type']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Classifier  $classifier
     * @return \Illuminate\Http\Response
     */
    public function show(Classifier $classifier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Classifier  $classifier
     * @return \Illuminate\Http\Response
     */
    public function edit(Classifier $classifier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classifier  $classifier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classifier $classifier)
    {	
    	if ( trim($request->input('value')) == '' )
    		$classifier->delete();
    	else
    		$classifier->update($request->except(['_token', '_method']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Classifier  $classifier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classifier $classifier)
    {
		$classifier->delete();
    }
}
