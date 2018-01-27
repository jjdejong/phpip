<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rule;
use Response;

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

    public function addShow()

    {
        $rule = new Rule ;
        $ruleComments = $rule->getTableComments('task_rules');
        return view('tables.ruleadd',compact('ruleComments'));
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rule $rule)
    {
    	    	
    	$rule->update($request->except(['_token', '_method']));
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'task' => 'required',
			'trigger_event' => 'required',
			'cost' => 'nullable|numeric',
			'years' => 'nullable|numeric',
			'months' => 'nullable|numeric',
			'days' => 'nullable|numeric',
			'fee' => 'nullable|numeric'
    	]);
    	$input = $request->all();
    	$to_retain = ['_token', '_method'];
    	if($validator->passes()){
			foreach ($input as $i =>$value) {				
				if (strpos($i, '_new')) {
					array_push($to_retain,$i);
				}
				if ($value == "...") {
					array_push($to_retain,$i);
				}
			}
			
			Rule::create($request->except($to_retain));
			return Response::json(['success' => '1']);
		}
		return Response::json(['errors' => $validator->errors()]);
    }
    
    public function create(Request $request)
    {
    	$rule = new Rule ;
        $rule->add();
    }

    public function delete(Rule $rule)
    {
    	$rule->delete();
    }

}
