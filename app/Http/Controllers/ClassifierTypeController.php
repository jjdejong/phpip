<?php

namespace App\Http\Controllers;

use App\Models\ClassifierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassifierTypeController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Type = $request->input('Type');
        $classifierType = ClassifierType::query();
        
        if (! is_null($Code)) {
            $classifierType = $classifierType->whereLike('code', $Code.'%');
        }

        if (! is_null($Type)) {
            $classifierType = $classifierType->whereJsonLike('type', $Type);
        }

        $types = $classifierType->with(['category:code,category'])->get();

        return view('classifier_type.index', compact('types'));
    }

    public function create()
    {
        $table = new ClassifierType;
        $tableComments = $table->getTableComments();

        return view('classifier_type.create', compact('tableComments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classifier_type|max:5',
            'type' => 'required|max:45',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return ClassifierType::create($request->except(['_token', '_method']));
    }

    public function show(ClassifierType $classifier_type)
    {
        $tableComments = $classifier_type->getTableComments();
        $classifier_type->load(['category:code,category']);

        return view('classifier_type.show', compact('classifier_type', 'tableComments'));
    }

    public function update(Request $request, ClassifierType $classifierType)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $classifierType->update($request->except(['_token', '_method']));

        return $classifierType;
    }

    public function destroy(ClassifierType $classifierType)
    {
        $classifierType->delete();

        return $classifierType;
    }
}
