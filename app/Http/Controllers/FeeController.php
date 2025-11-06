<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages official fee schedules for renewals.
 *
 * Fee tables define country-specific costs and fees for patent/trademark renewals
 * based on annuity year, category, origin, and validity periods. Supports SME
 * reductions and grace period surcharges.
 */
class FeeController extends Controller
{
    /**
     * Display a paginated list of fees with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $fees = new Fee;
        $filters = $request->except(['page']);
        if (! empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value != '') {
                    $fees = match ($key) {
                        'Origin' => $fees->where('for_origin', 'LIKE', "$value%"),
                        'Category' => $fees->where('for_category', 'LIKE', "$value%"),
                        'Qt' => $fees->where('qt', "$value%"),
                        'Country' => $fees->where('for_country', 'LIKE', "$value%"),
                        default => $fees->where($key, 'LIKE', "$value%"),
                    };
                }
            }
        }

        $query = $fees->orderBy('for_category')->orderBy('for_country')->orderBy('qt');

        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        $fees = $query->simplePaginate(config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')));

        return view('fee.index', compact('fees'));
    }

    /**
     * Show the form for creating a new fee entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Fee;
        $tableComments = $table->getTableComments();

        return view('fee.create', compact('tableComments'));
    }

    /**
     * Store newly created fee entries.
     *
     * Can create multiple entries at once when a range is specified (from_qt to to_qt).
     *
     * @param Request $request Fee data including category, country, quantity range, and costs
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'for_category' => 'required',
            'from_qt' => 'required|integer',
            'to_qt' => 'nullable|integer',
            'use_after' => 'nullable|date',
            'use_before' => 'nullable|date',
        ]);
        $request->merge(['creator' => Auth::user()->login]);
        if (is_null($request->input('to_qt'))) {
            $request->merge(['qt' => $request->input('from_qt')]);
            Fee::create($request->except(['from_qt', 'to_qt', '_token', '_method']));
        } else {
            for ($i = intval($request->input('from_qt')); $i <= intval($request->input('to_qt')); $i++) {
                $request->merge(['qt' => $i]);
                Fee::create($request->except(['from_qt', 'to_qt', '_token', '_method']));
            }
        }

        return response()->json(['success' => 'Fee created']);
    }

    /**
     * Display the specified fee entry.
     *
     * @param Fee $fee The fee to display
     * @return Fee
     */
    public function show(Fee $fee)
    {
        return $fee;
    }

    /**
     * Update the specified fee entry.
     *
     * @param Request $request Updated fee data
     * @param Fee $fee The fee to update
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Fee $fee)
    {
        $this->validate($request, [
            'use_after' => 'nullable|date',
            'use_before' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
            'cost_reduced' => 'nullable|numeric',
            'fee_reduced' => 'nullable|numeric',
            'cost_sup' => 'nullable|numeric',
            'fee_sup' => 'nullable|numeric',
            'cost_sup_reduced' => 'nullable|numeric',
            'fee_sup_reduced' => 'nullable|numeric',
        ]);
        $request->merge(['updater' => Auth::user()->login]);

        $fee->update($request->except(['_token', '_method']));

        return response()->json(['success' => 'Fee updated']);
    }

    /**
     * Remove the specified fee entry from storage.
     *
     * @param Fee $fee The fee to delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();

        return response()->json(['success' => 'Fee deleted']);
    }
}
