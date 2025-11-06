<?php

namespace App\Http\Controllers;

use App\Models\MatterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages matter type definitions.
 *
 * Matter types provide sub-classification within categories
 * (e.g., Utility Patent, Plant Patent, Word Mark, etc.).
 */
class MatterTypeController extends Controller
{
    /**
     * Display a list of matter types with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Type = $request->input('Type');
        $type = MatterType::query();

        if (! is_null($Code)) {
            $type = $type->whereLike('code', $Code.'%');
        }

        if (! is_null($Type)) {
            $type = $type->whereJsonLike('type', $Type);
        }

        $matter_types = $type->get();

        if ($request->wantsJson()) {
            return response()->json($matter_types);
        }

        return view('type.index', compact('matter_types'));
    }

    /**
     * Show the form for creating a new matter type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new MatterType;
        $tableComments = $table->getTableComments();

        return view('type.create', compact('tableComments'));
    }

    /**
     * Store a newly created matter type.
     *
     * @param Request $request Matter type data including code and type name
     * @return MatterType The created matter type
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:matter_type|max:5',
            'type' => 'required|max:45',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return MatterType::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified matter type.
     *
     * @param MatterType $type The matter type to display
     * @return \Illuminate\Http\Response
     */
    public function show(MatterType $type)
    {
        $tableComments = $type->getTableComments();

        return view('type.show', compact('type', 'tableComments'));
    }

    /**
     * Update the specified matter type.
     *
     * @param Request $request Updated matter type data
     * @param MatterType $type The matter type to update
     * @return MatterType The updated matter type
     */
    public function update(Request $request, MatterType $type)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $type->update($request->except(['_token', '_method']));

        return $type;
    }

    /**
     * Remove the specified matter type from storage.
     *
     * @param MatterType $type The matter type to delete
     * @return MatterType The deleted matter type
     */
    public function destroy(MatterType $type)
    {
        $type->delete();

        return $type;
    }
}
