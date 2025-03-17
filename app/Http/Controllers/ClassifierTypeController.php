<?php

namespace App\Http\Controllers;

use App\Models\ClassifierType;
use App\Models\Translations\ClassifierTypeTranslation;
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
            $classifierType = $classifierType->where('code', 'like', $Code.'%');
        }
        if (! is_null($Type)) {
            $classifierType = $classifierType->where('type', 'like', $Type.'%');
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
            'notes' => 'max:160'
        ]);
        
        $request->merge(['creator' => Auth::user()->login]);
        
        $classifierType = ClassifierType::create($request->except(['_token', '_method']));
        
        $translatableFields = ['type', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            ClassifierTypeTranslation::create([
                'code' => $classifierType->code,
                'locale' => $locale,
                ...$translations
            ]);
        }

        return response()->json(['redirect' => route('classifiertype.index')]);
    }

    public function show(ClassifierType $classifier_type)
    {
        $tableComments = $classifier_type->getTableComments();
        $classifier_type->load(['category:code,category']);

        return view('classifier_type.show', compact('classifier_type', 'tableComments'));
    }

    public function update(Request $request, ClassifierType $classifiertype)
    {
        $request->merge(['updater' => Auth::user()->login]);
        
        $translatableFields = ['type', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            ClassifierTypeTranslation::updateOrCreate(
                [
                    'code' => $classifiertype->code,
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
            $classifiertype->update($nonTranslatableData);
        }
        
        return $classifiertype;
    }

    public function destroy(ClassifierType $classifierType)
    {
        $classifierType->delete();

        return $classifierType;
    }
}
