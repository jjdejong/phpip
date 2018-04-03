<?php

namespace App\Http\Controllers;

use App\Task;
//use App\Event;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


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
    	$request->validate([
  			'trigger_id' => 'required|numeric',
  			'name' => 'required',
  			'due_date' => 'required|date',
  			'done_date' => 'nullable|date',
  			'cost' => 'nullable|numeric',
  			'fee' => 'nullable|numeric'
    	]);

    	Task::create($request->except(['_token', '_method', 'name']));
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
    	$this->validate($request, [
  			'due_date' => 'date',
  			'done_date' => 'nullable|date',
  			'cost' => 'nullable|numeric',
  			'fee' => 'nullable|numeric'
    	]);

    	// Remove task rule when due date is manually changed
    	if ($request->has('due_date'))
    		$request->request->add(['rule_used' => null]);

    	$task->update($request->except(['_token', '_method']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
    }
}
