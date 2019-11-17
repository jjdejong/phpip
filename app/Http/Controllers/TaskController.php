<?php

namespace App\Http\Controllers;

use App\Task;
//use App\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{

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
            'due_date' => 'required|date',
            'done_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return Task::create($request->except(['_token', '_method']));
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
        $request->merge([ 'updater' => Auth::user()->login ]);
        // Remove task rule when due date is manually changed
        if ($request->has('due_date')) {
            $request->merge(['rule_used' => null]);
        }

        $task->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Task updated']);
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
        return response()->json(['success' => 'Task deleted']);
    }
}
