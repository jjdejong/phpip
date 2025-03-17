<?php

namespace App\Http\Controllers;

use App\Models\MatterType;
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
            'type' => 'required|max:45',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return MatterType::create($request->except(['_token', '_method']));
    }

    public function show(MatterType $type)
    {
        $tableComments = $type->getTableComments();

        return view('type.show', compact('type', 'tableComments'));
    }

    public function update(Request $request, MatterType $type)
    {
        $request->merge(['updater' => Auth::user()->login]);
        
        // Define which fields are translatable
        $translatableFields = ['type'];
        
        // Process the update, separating translatable fields
        $nonTranslatableData = $type->updateTranslationFields(
            $request->except(['_token', '_method']), 
            $translatableFields
        );
        
        // Update non-translatable fields on the main model if there are any
        if (!empty($nonTranslatableData)) {
            $type->update($nonTranslatableData);
        }
        
        // Make sure we're returning the model with updated translations
        $type->refresh();
        
        return $type;
    }

    public function destroy(MatterType $type)
    {
        $type->delete();

        return $type;
    }
}
