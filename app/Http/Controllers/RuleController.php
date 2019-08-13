<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rule;
use Response;

class RuleController extends Controller
{
    public function index(Request $request)
    {
        $Task  = $request->input('Task');
        $Trigger = $request->input('Trigger');
        $Country = $request->input('Country');
        $Origin = $request->input('Origin');
        $Detail = $request->input('Detail');
        $Type = $request->input('Type');
        $Category = $request->input('Category');
        $rule = new Rule;
        if (! is_null($Task)) {
            $rule = $rule->whereHas('taskInfo', function ($q) use ($Task) {
                $q->where('name', 'like', $Task.'%');
            });
        }
        if (! is_null($Trigger)) {
            $rule = $rule->whereHas('trigger', function ($q) use ($Trigger) {
                $q->where('name', 'like', $Trigger.'%');
            });
        }
        if (! is_null($Country)) {
            $rule = $rule->whereHas('country', function ($q) use ($Country) {
                $q->where('name', 'like', $Country.'%');
            });
        }
        if (! is_null($Category)) {
            $rule = $rule->whereHas('category', function ($q) use ($Category) {
                $q->where('category', 'like', $Category.'%');
            });
        }
        if (! is_null($Detail)) {
            $rule = $rule->where('detail', 'like', $Detail.'%');
        }
        if (! is_null($Type)) {
            $rule = $rule->whereHas('type', function ($q) use ($Type) {
                $q->where('type', 'like', $Type.'%');
            });
        }
        if (! is_null($Origin)) {
            $rule = $rule->whereHas('origin', function ($q) use ($Origin) {
                $q->where('name', 'like', $Origin.'%');
            });
        }
        $ruleslist = $rule->with(['country:iso,name', 'trigger:code,name', 'category:code,category', 'origin:iso,name', 'type:code,type', 'taskInfo:code,name'])
            ->orderby('task')->get();
        return view('rule.index', compact('ruleslist'));
    }

    public function show(Rule $rule)
    {
        $ruleInfo = $rule->load([
          'trigger:code,name',
          'country:iso,name',
          'category:code,category',
          'origin:iso,name',
          'type:code,type',
          'taskInfo:code,name',
          'condition_eventInfo:code,name',
          'abort_onInfo:code,name',
          'responsibleInfo:id,name'
        ]);

        $ruleComments = $rule->getTableComments('task_rules');
        return view('rule.show', compact('ruleInfo', 'ruleComments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rule = new Rule ;
        $ruleComments = $rule->getTableComments('task_rules');
        return view('rule.create', compact('ruleComments'));
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
        $this->validate($request, [
            'cost' => 'nullable|numeric',
            'years' => 'nullable|numeric',
            'months' => 'nullable|numeric',
            'days' => 'nullable|numeric',
            'fee' => 'nullable|numeric'
        ]);

        $rule->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Rule updated']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'task' => 'required',
            'trigger_event' => 'required',
            'for_category' => 'required',
            'cost' => 'nullable|numeric',
            'years' => 'numeric',
            'months' => 'numeric',
            'days' => 'numeric',
            'fee' => 'nullable|numeric'
        ]);
        return Rule::create($request->except(['_token', '_method']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rule  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rule $rule)
    {
        $rule->delete();
        return response()->json(['success' => 'Rule deleted']);
    }
}
