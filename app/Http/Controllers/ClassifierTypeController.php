<?php

namespace App\Http\Controllers;

use App\ClassifierType;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;

class ClassifierTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Code  = $request->input('Code');
        $Type = $request->input('Type');
        $classifierType = ClassifierType::query() ;
        if (!is_null($Code)) {
            $classifierType = $classifierType->where('code', 'like', $Code.'%');
        }
        if (!is_null($Type)) {
            $classifierType = $classifierType->where('type', 'like', $Type.'%');
        }

        $types = $classifierType->with(['category:code,category'])->get();
        return view('classifier_type.index', compact('types'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('classifier_type');
        return view('classifier_type.create', compact('tableComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classifier_type|max:5',
            'type' => 'required|max:45',
            'main_display' => 'required'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return ClassifierType::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  ClassifierType $classifierType
     * @return \Illuminate\Http\Response
     */
    public function show(ClassifierType $classifier_type)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('classifier_type');
        $classifier_type->load(['category:code,category']);
        return view('classifier_type.show', compact('classifier_type', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassifierType $classifierType)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $classifierType->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'ClassifierType updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  ClassifierType $classifierType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassifierType $classifierType)
    {
        $classifierType->delete();
        return response()->json(['success' => 'ClassifierType deleted']);
    }
}
