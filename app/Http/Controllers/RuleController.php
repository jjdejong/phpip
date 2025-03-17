<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Translations\TaskRuleTranslation;
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
                $q->where('name', 'like', $Trigger . '%');
            });
        }
        if (!is_null($Country)) {
            $rule = $rule->whereHas('country', function ($q) use ($Country) {
                $q->where('name', 'like', $Country . '%');
            });
        }
        if (!is_null($Category)) {
            $rule = $rule->whereHas('category', function ($q) use ($Category) {
                $q->where('category', 'like', $Category . '%');
            });
        }
        if (!is_null($Detail)) {
            $rule = $rule->where('detail', 'like', $Detail . '%');
        }
        if (!is_null($Type)) {
            $rule = $rule->whereHas('type', function ($q) use ($Type) {
                $q->where('type', 'like', $Type . '%');
            });
        }
        if (!is_null($Origin)) {
            $rule = $rule->whereHas('origin', function ($q) use ($Origin) {
                $q->where('name', 'like', $Origin . '%');
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
        $request->merge(['updater' => Auth::user()->login]);
        
        $translatableFields = ['detail', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            TaskRuleTranslation::updateOrCreate(
                [
                    'task_rule_id' => $rule->id,
                    'locale' => $locale
                ],
                $translations
            );
        }
        
        $nonTranslatableData = array_diff_key(
            $request->except(['_token', '_method']),
            array_flip($translatableFields)
        );
        
        if (!empty($nonTranslatableData)) {
            $rule->update($nonTranslatableData);
        }
        
        return $rule;
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'task_id' => 'required|exists:task,id',
            'detail' => 'required',
            'for_client' => 'boolean'
        ]);
        
        $request->merge(['creator' => Auth::user()->login]);
        
        $rule = Rule::create($request->except(['_token', '_method']));
        
        $translatableFields = ['detail', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            TaskRuleTranslation::create([
                'task_rule_id' => $rule->id,
                'locale' => $locale,
                ...$translations
            ]);
        }

        return response()->json(['redirect' => route('rule.index')]);
    }

    public function destroy(Rule $rule)
    {
        Gate::authorize('admin');
        $rule->delete();

        return $rule;
    }
}
