<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use Illuminate\Http\Request;

class MatterSearchController extends Controller
{
    public function search(Request $request)
    {
        if ($request->search_field === 'Ref') {
            $matters = Matter::filter('caseref', 'asc', ['Ref' => $request->matter_search], false, true)->get();
            
            if ($matters->count() === 1) {
                return redirect('matter/' . $matters->first()->id);
            }
        }

        return redirect("/matter?{$request->search_field}={$request->matter_search}");
    }
}
