<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EventController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'code' => 'required',
            'eventName' => 'required',
            'matter_id' => 'required|numeric',
            'event_date' => 'required_without:alt_matter_id'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return Event::create($request->except(['_token', '_method', 'eventName']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event) {
        $this->validate($request, [
            'alt_matter_id' => 'nullable|numeric'
        ]);
        $request->merge([ 'updater' => Auth::user()->login ]);
        $event->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Event updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event) {
        $event->delete();
        return response()->json(['success' => 'Event deleted']);
    }

}
