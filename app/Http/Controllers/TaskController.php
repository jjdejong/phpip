<?php

namespace App\Http\Controllers;

use App\Task;
use App\Event;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tasks($id) // All events and their tasks, excepting renewals
    {
    	$events = Event::with(['tasks' => function($query) {
    		$query->where('code', '!=', 'REN');
    	}])->where('matter_id', $id)
    	->orderBy('event_date')->get();
    	return view('matter.tasks', compact('events'));
    }
    
    public function renewals($id) // The renewal trigger event and its renewals
    {
    	$events = Event::with('tasks')->whereHas('tasks', function($query) {
    		$query->where('code', 'REN');
	    })->where('matter_id', $id)->get();
	    return view('matter.tasks', compact('events'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
