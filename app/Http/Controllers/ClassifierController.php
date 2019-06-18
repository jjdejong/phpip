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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$this->validate($request, [
    		'matter_id' => 'required',
    		'type_code' => 'required',
    		'value' => 'required_without:lnk_matter_id'
    	]);

    	Classifier::create($request->except(['_token', '_method']));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classifier  $classifier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classifier $classifier)
    {
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
