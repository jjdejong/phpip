<?php

namespace App\Http\Controllers;

use App\EventClassLnk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EventClassController extends Controller
{
    public function store(Request $request)
    {
        return EventClassLnk::create($request->except(['_token', '_method', 'className']));
    }

    public function destroy(int $lnk)
    {
        if (EventClassLnk::destroy($lnk) == 1) {
            return response()->json(['success' => 'Link deleted']);
        } else {
            return response()->json(['error' => 'Deletion failed']);
        }
    }
}
