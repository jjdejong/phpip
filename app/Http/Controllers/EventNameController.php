<?php

namespace App\Http\Controllers;

use App\Models\EventClassLnk;
use App\Models\EventName;
use App\Models\Translations\EventNameTranslation;
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
        
        // Create the event name first
        $eventname = EventName::create($request->except(['_token', '_method']));
        
        // Handle translations
        $translatableFields = ['name', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        // Normalize to 'en' if it's an English variant
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        // Create translation record
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            EventNameTranslation::create([
                'code' => $eventname->code,
                'locale' => $locale,
                ...$translations
            ]);
        }

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
        
        $translatableFields = ['name', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        // Get translatable fields from request
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        
        if (!empty(array_filter($translations))) {
            // Update translation table
            EventNameTranslation::updateOrCreate(
                [
                    'code' => $eventname->code,
                    'locale' => $locale
                ],
                $translations
            );
            
            // Also update the main table with the same values
            $eventname->update($translations);
        }
        
        // Update non-translatable fields
        $nonTranslatableData = array_diff_key(
            $request->except(['_token', '_method']),
            array_flip($translatableFields)
        );
        
        if (!empty($nonTranslatableData)) {
            $eventname->update($nonTranslatableData);
        }
        
        return $eventname;
    }

    public function destroy(EventName $eventname)
    {
        $eventname->delete();

        return $eventname;
    }
}
