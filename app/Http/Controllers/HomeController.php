<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;
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
        $user_dashboard = $request->user_dashboard;

        // Get list of active tasks
        $task = new Task;
        $tasks = $task->openTasks(false, $MyTasks, $user_dashboard)->take(100)->get();

        // Get list of active renewals
        $renewals = $task->openTasks(true, $MyTasks, $user_dashboard)->take(200)->get();

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
