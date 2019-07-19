<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\EventName;
use App\Actor;
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
        $Code  = $request->input ( 'Code' );
        $Name = $request->input ( 'Name' );
        $ename = EventName::query() ;
        if ( !is_null($Code)) {
			$ename = $ename->where('code','like', $Code.'%');
		}
        if ( !is_null($Name)) {
			$ename = $ename->where('name', 'like', $Name.'%');
		}

        $enameslist = $ename->get();
        return view('eventname.index', compact('enameslist') );
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
        return view('eventname.create',compact('tableComments'));
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
    	$input = $request->all();
    	$to_retain = ['_method'];

			foreach ($input as $i =>$value) {
				if (strpos($i, '_new')) {
					array_push($to_retain,$i);
				}
				if ($value == "...") {
					array_push($to_retain,$i);
				}
			}

			return EventName::create($request->except($to_retain));
		}

    /**
     * Display the specified resource.
     *
     * @param  \App\EventName  $eventName
     * @return \Illuminate\Http\Response
     */
    public function show($n)

    {
        $ename = new EventName ;
        $table = new Actor ;
        $enameInfo = $ename->with('countryInfo')->with('categoryInfo')->with('default_responsibleInfo')->find($n);
        $tableComments = $table->getTableComments('event_name');
        return view('eventname.show', compact('enameInfo', 'tableComments') );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EventName  $eventName
     * @return \Illuminate\Http\Response
     */
    public function edit(EventName $eventName)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EventName  $eventName
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventName $eventName)
    {
    		$eventName->update($request->except(['_token', '_method']));
    		return response()->json(['success' => 'Event name updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventName  $eventName
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventName $eventName)
    {
        $eventName->delete();
        return response()->json(['success' => 'Event name deleted']);
    }
}
