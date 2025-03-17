<?php

namespace App\Http\Controllers;

use App\Models\EventClassLnk;
use App\Models\EventName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventNameController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Name = $request->input('Name');
        $ename = EventName::query();
        if (! is_null($Code)) {
            $ename = $ename->where('code', 'like', $Code.'%');
        }
        if (! is_null($Name)) {
            $ename = $ename->where('name', 'like', $Name.'%');
        }

        $enameslist = $ename->paginate(21);
        $enameslist->appends($request->input())->links();

        return view('eventname.index', compact('enameslist'));
    }

    public function create()
    {
        $table = new EventName;
        $tableComments = $table->getTableComments();

        return view('eventname.create', compact('tableComments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:event_name|max:5',
            'name' => 'required|max:45',
            'notes' => 'max:160',
        ]);
        $request->merge(['creator' => Auth::user()->login]);
        EventName::create($request->except(['_token', '_method']));

        return response()->json(['redirect' => route('eventname.index')]);
    }

    public function show(EventName $eventname)
    {
        $tableComments = $eventname->getTableComments();
        $eventname->load(['countryInfo:iso,name', 'categoryInfo:code,category', 'default_responsibleInfo:id,name']);
        $links = EventClassLnk::where('event_name_code', '=', $eventname->code)->get();

        return view('eventname.show', compact('eventname', 'tableComments', 'links'));
    }

    public function update(Request $request, EventName $eventname)
    {
        $request->merge(['updater' => Auth::user()->login]);
        
        // Define which fields are translatable
        $translatableFields = ['name', 'notes'];
        
        // Process the update, separating translatable fields
        $nonTranslatableData = $eventname->updateTranslationFields(
            $request->except(['_token', '_method']), 
            $translatableFields
        );
        
        // Update non-translatable fields on the main model if there are any
        if (!empty($nonTranslatableData)) {
            $eventname->update($nonTranslatableData);
        }
        
        // Make sure we're returning the model with updated translations
        $eventname->refresh();
        
        return $eventname;
    }

    public function destroy(EventName $eventname)
    {
        $eventname->delete();

        return $eventname;
    }
}
