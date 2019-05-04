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
        $Origin = $request->input ( 'Origin' );
        $Detail = $request->input ( 'Detail' );
        $Type = $request->input ( 'Type' );
        $Category = $request->input ( 'Category' );
        $rule = new Rule ;
        if (! is_null($Task)) {
			$rule = $rule->whereHas('taskInfo', function($q) use ($Task) {$q->where('name','like',$Task.'%');});
			}
        if (! is_null($Trigger)) {
			$rule = $rule->whereHas('trigger', function($q) use ($Trigger) {$q->where('name','like',$Trigger.'%');});
			}
        if (! is_null($Country)) {
			$rule = $rule->whereHas('country', function($q) use ($Country) {$q->where('name','like',$Country.'%');});
			}
        if (! is_null($Category)) {
			$rule = $rule->whereHas('category', function($q) use ($Category) {$q->where('category','like',$Category.'%');});
			}
        if (! is_null($Detail)) {
			$rule = $rule->where('detail','like',$Detail.'%');
			}
        if (! is_null($Type)) {
			$rule = $rule->whereHas('type', function($q) use ($Type) {$q->where('type','like',$Type.'%');});
			}
        if (! is_null($Origin)) {
			$rule = $rule->whereHas('origin', function($q) use ($Origin) {$q->where('name','like',$Origin.'%');});
			}
        $ruleslist = $rule->with('country:iso,name', 'trigger:code,name','category:code,category', 'origin:iso,name', 'type', 'taskInfo:code,name')
			->orderby('task')->get();
        return view('rule.index', compact('ruleslist') );
    }

    public function show($n)

    {
        $rule = new Rule ;
        $ruleInfo = $rule->with('country', 
			'trigger',
			'country',
			'category',
			'origin', 
			'type', 
			'taskInfo',
			'condition_eventInfo',
			'abort_onInfo',
			'responsibleInfo'
			)->find($n);
		//	$rule->getRuleInfo($n);
        
        $ruleComments = $rule->getTableComments('task_rules');
        return view('rule.show', compact('ruleInfo', 'ruleComments') );
    }

    public function create()

    {
        $rule = new Rule ;
        $ruleComments = $rule->getTableComments('task_rules');
        return view('rule.create',compact('ruleComments'));
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
    
/*    public function create(Request $request)
    {
    	$rule = new Rule ;
        $rule->add();
    }
*/
    public function destroy($n)
    {
    	$rule = new Rule ;
    	$rule->destroy($n);
    }

}
