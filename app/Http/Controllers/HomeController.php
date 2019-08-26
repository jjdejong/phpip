<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
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
        // Filters
        $MyTasks = $request->input ( 'my_tasks' );
        $MyRenewals = $request->input ( 'my_renewals' );

        $userid = Auth::user()->id;
        $role = Auth::user()->default_role;

        // Get list of active tasks
        $tasks = Task::with(['info:code,name', 'trigger.matter:id,caseref,suffix', 'trigger.matter.client:id,actor_id'])->whereHas('trigger.matter', function ($query) {
          $query->where('dead', 0);
        })->where('done', 0)->where('task.code', '!=', 'REN')->orderby('due_date');
        if ($MyTasks) {
            $tasks->where('assigned_to', Auth::user()->login);
        }
        if ($role == 'CLI') {
            $tasks->whereHas('trigger.matter.client', function ($query) use ($userid) {
              $query->where('actor_id', $userid);
            });
        }
        $tasks = $tasks->simplePaginate(25)->all();

        // Get list of active renewals
        $renewals = Task::with(['info:code,name', 'trigger.matter:id,caseref,suffix', 'trigger.matter.client:id,actor_id'])->whereHas('trigger.matter', function ($query) {
          $query->where('dead', 0);
        })->where('done', 0)->where('task.code', 'REN')->orderby('due_date');
        if ($MyRenewals) {
            $renewals->where('assigned_to', Auth::user()->login);
        }
        if ($role == 'CLI') {
            $renewals->whereHas('trigger.matter.client', function ($query) use ($userid) {
              $query->where('actor_id', $userid);
            });
        }
        $renewals = $renewals->simplePaginate(25)->all();
        // Count matters per categories
        $categories = Matter::getCategoryMatterCount();
        $taskscount = Task::getUsersOpenTaskCount();
        return view('home',compact('tasks','renewals','categories','taskscount'));
    }

    /**
     * Clear selected tasks.
     *
     */
    public function clearTasks(Request $request)
    {
    	$validator = Validator::make($request->all(), [
        'done_date' => 'bail|required|date',
        ]);
    	if($validator->passes()){
            $tids = $request->input('task_ids');
            $done_date = $request->input('done_date');
            $updated = 0;
            foreach($tids as $id) {
                $task = Task::find($id);
                $task->done_date = $done_date;
                $task->done = 1;
                $returncode = $task->save();
                if ($returncode) $updated = $updated + 1;
            }
            return Response::json(['not_updated' => (count($tids) - $updated),
            'errors' =>'']);
		}
		return Response::json(['errors' => $validator->errors()]);

    }

}
