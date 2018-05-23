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
    	$validator = Validator::make($request->all(), [
			'code' => 'required|unique:event_name|max:5',
			'name' => 'required|max:45',
			'notes' => 'max:160'
    	]);
    	$input = $request->all();
    	$to_retain = ['_method'];
    	if($validator->passes()){
			foreach ($input as $i =>$value) {				
				if (strpos($i, '_new')) {
					array_push($to_retain,$i);
				}
				if ($value == "...") {
					array_push($to_retain,$i);
				}
			}
			
			EventName::create($request->except($to_retain));
			return Response::json(['success' => '1']);
		}
		return Response::json(['errors' => $validator->errors()]);
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
    public function update(Request $request, $id)
    {
		$eventName = new EventName ;
		
		$result = $eventName::find($id)->update($request->except(['_token', '_method']));
		return Response::json(['success' => $result]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventName  $eventName
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $affected = EventName::destroy($id);
        return Response::json(['deleted' => $affected ]);
    }
}
