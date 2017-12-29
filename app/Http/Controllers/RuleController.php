<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rule;

class RuleController extends Controller
{
    //
    public function index(Request $request)

    {
        $Task  = $request->input ( 'Task' );
        $Trigger = $request->input ( 'Trigger' );
        $Country = $request->input ( 'Country' );
        $rule = new Rule ;
        $ruleslist = $rule->rulesList($Task, $Trigger, $Country);
        return view('tables.rulelist', compact('ruleslist') );
    }

    public function show($n)

    {
        $rule = new Rule ;
        $ruleInfo = $rule->getRuleInfo($n);
        $ruleComments = $rule->getTableComments('task_rules');
        return view('tables.ruleinfo', compact('ruleInfo', 'ruleComments') );
    }

    public function add()

    {
        $rule = new Rule ;
        $ruleComments = $rule->getTableComments('task_rules');
        return view('tables.ruleadd',compact('ruleComments'));
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rule $rule)
    {
    	    	
    	$rule->update($request->except(['_token', '_method']));
    }

    public function delete(Rule $rule)
    {
    	$rule->delete();
    }

}
