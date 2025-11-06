<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing events in matters.
 *
 * Handles CRUD operations for events such as filing, publication, grant, priority claims,
 * and other milestone dates associated with IP matters.
 */
class EventController extends Controller
{
    /**
     * Store a new event in the database.
     *
     * @param Request $request The HTTP request containing event data.
     * @return Event The newly created event model.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'eventName' => 'required',
            'matter_id' => 'required|numeric',
            'event_date' => 'required_without:alt_matter_id',
        ]);
        if ($request->filled('event_date')) {
            $request->merge(['event_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->event_date)]);
        }
        $request->merge(['creator' => Auth::user()->login]);

        return Event::create($request->except(['_token', '_method', 'eventName']));
    }

    /**
     * Display detailed information for a specific event.
     *
     * @param Event $event The event to display.
     * @return void Not implemented.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Update an event in the database.
     *
     * @param Request $request The HTTP request containing updated event data.
     * @param Event $event The event to update.
     * @return Event The updated event model.
     */
    public function update(Request $request, Event $event)
    {
        $this->validate($request, [
            'alt_matter_id' => 'nullable|numeric',
            'event_date' => 'sometimes|required_without:alt_matter_id',
        ]);
        if ($request->filled('event_date')) {
            $request->merge(['event_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->event_date)]);
        }
        $request->merge(['updater' => Auth::user()->login]);
        $event->update($request->except(['_token', '_method']));

        return $event;
    }

    /**
     * Remove an event from the database.
     *
     * @param Event $event The event to delete.
     * @return Event The deleted event model.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return $event;
    }
}
