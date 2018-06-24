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
        $tasks = Task::with('info','trigger', 'trigger.matter')
        ->where('done','0')->where('code', '!=', 'REN');
        if (! is_null($MyTasks)) {
            if ($MyTasks =='1') $tasks = $tasks->where('assigned_to','=',Auth::user()->login);}
        $tasks = $tasks->orderby('due_date')->get();
        // Get list of active renewals
        $renewals = Task::with('info','trigger', 'trigger.matter')
        ->where('done','0')->where('code', '=', 'REN');
        if (! is_null($MyRenewals)) {
            if ($MyRenewals =='1') $renewals = $renewals->where('assigned_to','=',Auth::user()->login);}
        $renewals = $renewals->orderby('due_date')->get();
        
        return view('home',compact('tasks','renewals'));
    }
}
