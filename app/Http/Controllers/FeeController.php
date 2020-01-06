<?php

namespace App\Http\Controllers;

use App\Fee;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FeeController extends Controller
{

    public function index(Request $request)
    {
        $fees = new Fee;
        $filters = $request->except(['page']);
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value != '') {
                    switch($key) {
                        case 'Origin':
                            $fees = $fees->where('for_origin', 'LIKE', "$value%");
                            break;
                        case 'Category':
                            $fees = $fees->where('for_category', 'LIKE', "$value%");
                            break;
                        case 'Qt':
                            $fees = $fees->where('qt', "$value%");
                            break;
                        case 'Country':
                            $fees = $fees->where('for_country', 'LIKE', "$value%");
                            break;
                        default:
                            $fees = $fees->where($key, 'LIKE', "$value%");
                            break;
                    }
                }
            }
        }
        $fees = $fees->orderBy('for_category')->orderBy('for_country')->orderBy('qt')->simplePaginate( config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')) );
        return view('fee.index', compact('fees'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('fees');
        return view('fee.create', compact('tableComments'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
        $request->merge([ 'creator' => Auth::user()->login ]);
        if(is_null($request->input('to_qt'))) {
          $request->merge([ 'qt' =>   $request->input('from_qt') ]);
          Fee::create($request->except(['from_qt','to_qt','_token', '_method']));
        }
        else {
          for ($i= intval($request->input('from_qt')); $i <=  intval($request->input('to_qt')); $i++) {
              $request->merge([ 'qt' =>   $i ]);
              Fee::create($request->except(['from_qt','to_qt','_token', '_method']));
          }
        }
        return response()->json(['success' => 'Fee created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function show(Fee $fee)
    {
        return $fee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
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
        $request->merge([ 'updater' => Auth::user()->login ]);

        $fee->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Fee updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();
        return response()->json(['success' => 'Fee deleted']);
    }
}
