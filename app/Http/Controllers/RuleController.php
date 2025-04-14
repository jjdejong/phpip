<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RuleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('readonly');
        $Task = $request->input('Task');
        $Trigger = $request->input('Trigger');
        $Country = $request->input('Country');
        $Origin = $request->input('Origin');
        $Detail = $request->input('Detail');
        $Type = $request->input('Type');
        $Category = $request->input('Category');
        $rule = new Rule;
        if (!is_null($Task)) {
            $rule = $rule->whereHas('taskInfo', function ($q) use ($Task) {
                $q->where('name', 'like', $Task . '%');
            });
        }
        if (!is_null($Trigger)) {
            $rule = $rule->whereHas('trigger', function ($q) use ($Trigger) {
                $q->whereLike('name', "{$Trigger}%");
            });
        }
        if (!is_null($Country)) {
            $rule = $rule->whereHas('country', function ($q) use ($Country) {
                $q->whereLike('name', "{$Country}%");
            });
        }
        if (!is_null($Category)) {
            $rule = $rule->whereHas('category', function ($q) use ($Category) {
                $q->whereLike('category', "{$Category}%");
            });
        }
        if (!is_null($Detail)) {
            $rule = $rule->whereLike('detail', "{$Detail}%");
        }
        if (!is_null($Type)) {
            $rule = $rule->whereHas('type', function ($q) use ($Type) {
                $q->whereLike('type', "{$Type}%");
            });
        }
        if (!is_null($Origin)) {
            $rule = $rule->whereHas('origin', function ($q) use ($Origin) {
                $q->whereLike('name', "{$Origin}%");
            });
        }
        $ruleslist = $rule->with(['country:iso,name', 'trigger:code,name', 'category:code,category', 'origin:iso,name', 'type:code,type', 'taskInfo:code,name'])
            ->orderby('task')->paginate(21);
        $ruleslist->appends($request->input())->links();

        return view('rule.index', compact('ruleslist'));
    }

    public function show(Rule $rule)
    {
        Gate::authorize('readonly');
        $ruleInfo = $rule->load([
            'trigger:code,name',
            'country:iso,name',
            'category:code,category',
            'origin:iso,name',
            'type:code,type',
            'taskInfo:code,name',
            'condition_eventInfo:code,name',
            'abort_onInfo:code,name',
            'responsibleInfo:login,name',
        ]);

        $ruleComments = $rule->getTableComments();

        return view('rule.show', compact('ruleInfo', 'ruleComments'));
    }

    public function create()
    {
        Gate::authorize('admin');
        $rule = new Rule;
        $ruleComments = $rule->getTableComments();

        return view('rule.create', compact('ruleComments'));
    }

    public function update(Request $request, Rule $rule)
    {
        Gate::authorize('admin');
        $this->validate($request, [
            'task' => 'sometimes|required',
            'trigger_event' => 'sometimes|required',
            'for_category' => 'sometimes|required',
            'cost' => 'nullable|numeric',
            'years' => 'nullable|numeric',
            'months' => 'nullable|numeric',
            'days' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
            'use_before' => 'nullable|date',
            'use_after' => 'nullable|date',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        $rule->update($request->except(['_token', '_method']));

        return $rule;
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');
        $this->validate($request, [
            'task' => 'required',
            'trigger_event' => 'required',
            'for_category' => 'required',
            'cost' => 'nullable|numeric',
            'years' => 'numeric',
            'months' => 'numeric',
            'days' => 'numeric',
            'fee' => 'nullable|numeric',
            'use_before' => 'nullable|date',
            'use_after' => 'nullable|date',
        ]);
        $request->merge(['creator' => Auth::user()->login]);
        Rule::create($request->except(['_token', '_method']));

        return response()->json(['redirect' => route('rule.index')]);
    }

    public function destroy(Rule $rule)
    {
        Gate::authorize('admin');
        $rule->delete();

        return $rule;
    }
}
