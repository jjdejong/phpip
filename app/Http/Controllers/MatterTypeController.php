<?php

namespace App\Http\Controllers;

use App\Models\MatterType;
use App\Models\Translations\MatterTypeTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatterTypeController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Type = $request->input('Type');
        $type = MatterType::query();
        if (! is_null($Code)) {
            $type = $type->where('code', 'like', $Code.'%');
        }
        if (! is_null($Type)) {
            $type = $type->where('type', 'like', $Type.'%');
        }

        $matter_types = $type->get();

        return view('type.index', compact('matter_types'));
    }

    public function create()
    {
        $table = new MatterType;
        $tableComments = $table->getTableComments();

        return view('type.create', compact('tableComments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:matter_type|max:5',
            'type' => 'required|max:45'
        ]);
        
        $request->merge(['creator' => Auth::user()->login]);
        
        $matterType = MatterType::create($request->except(['_token', '_method']));
        
        $translatableFields = ['type'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            MatterTypeTranslation::create([
                'code' => $matterType->code,
                'locale' => $locale,
                ...$translations
            ]);
        }

        return response()->json(['redirect' => route('mattertype.index')]);
    }

    public function show(MatterType $type)
    {
        $tableComments = $type->getTableComments();

        return view('type.show', compact('type', 'tableComments'));
    }

    public function update(Request $request, MatterType $mattertype)
    {
        $request->merge(['updater' => Auth::user()->login]);
        
        $translatableFields = ['type'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            MatterTypeTranslation::updateOrCreate(
                [
                    'code' => $mattertype->code,
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
            $mattertype->update($nonTranslatableData);
        }
        
        return $mattertype;
    }

    public function destroy(MatterType $type)
    {
        $type->delete();

        return $type;
    }
}
