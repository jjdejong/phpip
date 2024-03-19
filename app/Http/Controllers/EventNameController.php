<?php

namespace App\Http\Controllers;

use App\Actor;
use App\EventClassLnk;
use App\EventName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        $table = new Actor;
        $tableComments = $table->getTableComments('event_name');

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
        $table = new Actor;
        $tableComments = $table->getTableComments('event_name');
        $eventname->load(['countryInfo:iso,name', 'categoryInfo:code,category', 'default_responsibleInfo:id,name']);
        $links = EventClassLnk::where('event_name_code', '=', $eventname->code)->get();

        return view('eventname.show', compact('eventname', 'tableComments', 'links'));
    }

    public function update(Request $request, EventName $eventname)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $eventname->update($request->except(['_token', '_method']));

        return $eventname;
    }

    public function destroy(EventName $eventname)
    {
        $eventname->delete();

        return $eventname;
    }
}
