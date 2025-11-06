<?php

namespace App\Http\Controllers;

use App\Models\EventClassLnk;
use Illuminate\Http\Request;

/**
 * Manages links between event names and template classes.
 *
 * Creates associations to automatically suggest document templates
 * when specific events occur on matters.
 */
class EventClassController extends Controller
{
    /**
     * Create a link between an event name and template class.
     *
     * @param Request $request Link data excluding className (display only)
     * @return EventClassLnk The created link
     */
    public function store(Request $request)
    {
        return EventClassLnk::create($request->except(['_token', '_method', 'className']));
    }

    /**
     * Remove the specified event-class link.
     *
     * @param int $lnk The link ID to delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $lnk)
    {
        if (EventClassLnk::destroy($lnk) == 1) {
            return response()->json(['success' => 'Link deleted']);
        } else {
            return response()->json(['error' => 'Deletion failed']);
        }
    }
}
