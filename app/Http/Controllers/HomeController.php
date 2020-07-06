<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Matter;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Count matters per categories
        $categories = Matter::getCategoryMatterCount();
        $taskscount = Task::getUsersOpenTaskCount();
        return view('home', compact('categories', 'taskscount'));
    }

    /**
     * Clear selected tasks.
     *
     */
    public function clearTasks(Request $request)
    {
        $this->validate($request, [
            'done_date' => 'bail|required',
        ]);
        $tids = $request->task_ids;
        $done_date = $request->done_date;
        $updated = 0;
        foreach ($tids as $id) {
            $task = Task::find($id);
            $task->done_date = $done_date;
            $returncode = $task->save();
            if ($returncode) {
                $updated++;
            }
        }
        return response()->json(['not_updated' => (count($tids) - $updated), 'errors' =>'']);
    }
}
