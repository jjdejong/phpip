<?php

namespace App\Http\Controllers;

use App\Models\EventClassLnk;
use App\Models\EventName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages event name definitions.
 *
 * Event names represent procedural milestones in matter lifecycle (filing, grant,
 * publication, etc.). Used as triggers for task rules and workflow automation.
 */
class EventNameController extends Controller
{
    /**
     * Display a paginated list of event names with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Name = $request->input('Name');
        $ename = EventName::query();
        if (! is_null($Code)) {
            $ename = $ename->whereLike('code', $Code.'%');
        }

        if (! is_null($Name)) {
            $ename = $ename->whereJsonLike('name', $Name);
        }

        if ($request->wantsJson()) {
            return response()->json($ename->get());
        }

        $enameslist = $ename->paginate(21);
        $enameslist->appends($request->input())->links();

        return view('eventname.index', compact('enameslist'));
    }

    /**
     * Show the form for creating a new event name.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new EventName;
        $tableComments = $table->getTableComments();

        return view('eventname.create', compact('tableComments'));
    }

    /**
     * Store a newly created event name.
     *
     * @param Request $request Event name data including code, name, and notes
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Display the specified event name with template class links.
     *
     * @param EventName $eventname The event name to display
     * @return \Illuminate\Http\Response
     */
    public function show(EventName $eventname)
    {
        $tableComments = $eventname->getTableComments();
        $eventname->load(['countryInfo:iso,name', 'categoryInfo:code,category', 'default_responsibleInfo:id,name']);
        $links = EventClassLnk::where('event_name_code', '=', $eventname->code)->get();

        return view('eventname.show', compact('eventname', 'tableComments', 'links'));
    }

    /**
     * Update the specified event name.
     *
     * @param Request $request Updated event name data
     * @param EventName $eventname The event name to update
     * @return EventName The updated event name
     */
    public function update(Request $request, EventName $eventname)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $eventname->update($request->except(['_token', '_method']));

        return $eventname;
    }

    /**
     * Remove the specified event name from storage.
     *
     * @param EventName $eventname The event name to delete
     * @return EventName The deleted event name
     */
    public function destroy(EventName $eventname)
    {
        $eventname->delete();

        return $eventname;
    }
}
