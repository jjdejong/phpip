<?php

namespace App\Http\Controllers;

use App\MatterType;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;

class MatterTypeController extends Controller
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
        $type = MatterType::query() ;
        if (!is_null($Code)) {
            $type = $type->where('code', 'like', $Code.'%');
        }
        if (!is_null($Type)) {
            $type = $type->where('type', 'like', $Type.'%');
        }

        $matter_types = $type->get();
        return view('type.index', compact('matter_types'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('matter_type');
        return view('type.create', compact('tableComments'));
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
            'code' => 'required|unique:matter_type|max:5',
            'type' => 'required|max:45',
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return MatterType::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  MatterType $type
     * @return \Illuminate\Http\Response
     */
    public function show(MatterType $type)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('matter_type');
        return view('type.show', compact('type', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MatterType $type)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $type->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Matter Type updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  MatterType $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(MatterType $type)
    {
        $type->delete();
        return response()->json(['success' => 'Matter Type deleted']);
    }
}
