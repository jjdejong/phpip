<?php

namespace App\Http\Controllers;

use App\EventName;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;

class EventNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Code  = $request->input('Code');
        $Name = $request->input('Name');
        $ename = EventName::query() ;
        if (!is_null($Code)) {
            $ename = $ename->where('code', 'like', $Code.'%');
        }
        if (!is_null($Name)) {
            $ename = $ename->where('name', 'like', $Name.'%');
        }

        $enameslist = $ename->get();
        return view('eventname.index', compact('enameslist'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('event_name');
        return view('eventname.create', compact('tableComments'));
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
            'code' => 'required|unique:event_name|max:5',
            'name' => 'required|max:45',
            'notes' => 'max:160'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        EventName::create($request->except(['_token', '_method']));
        return response()->json(['redirect' => route('eventname.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EventName  $eventname
     * @return \Illuminate\Http\Response
     */
    public function show(EventName $eventname)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('event_name');
        $eventname->load(['countryInfo:iso,name', 'categoryInfo:code,category', 'default_responsibleInfo:id,name']);
        return view('eventname.show', compact('eventname', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EventName  $eventname
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventName $eventname)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $eventname->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Event name updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventName  $eventname
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventName  $eventname)
    {
        $eventname->delete();
        return response()->json(['success' => 'Event name deleted']);
    }
}
