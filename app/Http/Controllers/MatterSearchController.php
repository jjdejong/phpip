<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use Illuminate\Http\Request;

/**
 * Handles quick matter search functionality.
 *
 * Provides fast matter lookup by reference with smart redirection
 * to matter detail page for single results.
 */
class MatterSearchController extends Controller
{
    /**
     * Search for matters and redirect appropriately.
     *
     * If search by Ref finds exactly one matter, redirects directly to that matter.
     * Otherwise redirects to matter list with search parameters.
     *
     * @param Request $request Contains search_field and matter_search parameters
     * @return \Illuminate\Http\RedirectResponse
     */
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
