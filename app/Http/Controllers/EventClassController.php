<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventClassLnk;
use Log;

class EventClassController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      return EventClassLnk::create($request->except(['_token', '_method','className']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $lnk)
    {
        if (EventClassLnk::destroy($lnk) == 1) {
          return response()->json(['success' => 'Link deleted']);
        }
        else {
          return response()->json(['error' => 'Deletion failed']);
        }
    }
}
