<?php

namespace App\Http\Controllers;

use App\Classifier;
use Illuminate\Support\Facades\Auth;
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
    		'value' => 'required_without_all:lnk_matter_id,image',
        'image'  => 'image|max:1024'
    	]);
      if ($request->hasFile('image')) {
        $file = $request->file('image');
        $request->merge([ 'value' => $file->getMimeType() ]);
        $request->merge([ 'img' => $file->openFile()->fread($file->getSize()) ]);
      }
      $request->merge([ 'creator' => Auth::user()->login ]);
    	return Classifier::create($request->except(['_token', '_method', 'image']))->id;
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
      if ($classifier->type->main_display && !$request->filled('value')) {
        $classifier->delete();
      } else {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $classifier->update($request->except(['_token', '_method']));
      }
      return $classifier;
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
        return response()->json(['success' => 'Classifier deleted']);
    }
}
