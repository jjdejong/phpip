<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $task = new Task;
        $isrenewals = $request->isrenewals;
        $tasks = $task->openTasks($isrenewals, $request->what_tasks, $request->user_dashboard)->simplePaginate(18);
        $tasks->appends($request->input())->links(); // Keep URL parameters in the paginator links

        return view('task.index', compact('tasks', 'isrenewals'));
    }

    public function store(Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $request->validate([
            'trigger_id' => 'required|numeric',
            'due_date' => 'required',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
        ]);
        $request->merge(['due_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->due_date)]);
        if ($request->filled('done_date')) {
            $request->merge(['done_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->done_date)]);
        }
        $request->merge(['creator' => Auth::user()->login]);

        return Task::create($request->except(['_token', '_method']));
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $this->validate($request, [
            'due_date' => 'sometimes|filled',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        if ($request->filled('done_date')) {
            $request->merge(['done_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->done_date)]);
        }
        // Remove task rule when due date is manually changed
        if ($request->filled('due_date')) {
            $request->merge(['due_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->due_date)]);
            $request->merge(['rule_used' => null]);
        }
        // Remove renewal from renewal management pipeline
        if (($request->filled('done_date') || $request->done) && $task->code == 'REN') {
            $request->merge(['step' => -1]);
        }
        $task->update($request->except(['_token', '_method']));

        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return $task;
    }
}
