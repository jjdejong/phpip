<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Task;

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
        
        // Get list of active tasks
        $tasks = Task::with('info:code,name', 'trigger:id,matter_id', 'trigger.matter:id,caseref,suffix')
        ->where('done','0')->where('code', '!=', 'REN')->orderby('due_date');
        if ($MyTasks) {
            $tasks->where('assigned_to', Auth::user()->login);
        }
        $tasks = $tasks->simplePaginate(25);
        // Get list of active renewals
        $renewals = Task::with('info:code,name', 'trigger:id,matter_id', 'trigger.matter:id,caseref,suffix')
        ->where('done','0')->where('code', 'REN')->orderby('due_date');
        if ($MyRenewals) {
            $renewals->where('assigned_to', Auth::user()->login);
        }
        $renewals = $renewals->simplePaginate(25);
        
        return view('home',compact('tasks','renewals'));
    }
}
