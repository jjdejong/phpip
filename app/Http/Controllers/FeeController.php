<?php

namespace App\Http\Controllers;

use App\Actor;
use App\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        App::setLocale(Auth::user()->language);
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
        $fees = $fees->orderBy('for_category')->orderBy('for_country')->orderBy('qt')->simplePaginate(config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')));

        return view('fee.index', compact('fees'));
    }

    public function create()
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('fees');

        return view('fee.create', compact('tableComments'));
    }

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

    public function show(Fee $fee)
    {
        return $fee;
    }

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

    public function destroy(Fee $fee)
    {
        $fee->delete();

        return response()->json(['success' => 'Fee deleted']);
    }
}
