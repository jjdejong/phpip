<?php

namespace App\Http\Controllers;

use App\Models\ClassifierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages classifier type definitions.
 *
 * Defines types of classifiers that can be attached to matters, such as
 * keywords, URLs, or image fields. Controls display behavior and categorization.
 */
class ClassifierTypeController extends Controller
{
    /**
     * Display a list of classifier types with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Type = $request->input('Type');
        $classifierType = ClassifierType::query();

        if (! is_null($Code)) {
            $classifierType = $classifierType->whereLike('code', $Code.'%');
        }

        if (! is_null($Type)) {
            $classifierType = $classifierType->whereJsonLike('type', $Type);
        }

        $types = $classifierType->with(['category:code,category'])->get();

        if ($request->wantsJson()) {
            return response()->json($types);
        }

        return view('classifier_type.index', compact('types'));
    }

    /**
     * Show the form for creating a new classifier type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new ClassifierType;
        $tableComments = $table->getTableComments();

        return view('classifier_type.create', compact('tableComments'));
    }

    /**
     * Store a newly created classifier type.
     *
     * @param Request $request Classifier type data including code and type name
     * @return ClassifierType The created classifier type
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classifier_type|max:5',
            'type' => 'required|max:45',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return ClassifierType::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified classifier type.
     *
     * @param ClassifierType $classifier_type The classifier type to display
     * @return \Illuminate\Http\Response
     */
    public function show(ClassifierType $classifier_type)
    {
        $tableComments = $classifier_type->getTableComments();
        $classifier_type->load(['category:code,category']);

        return view('classifier_type.show', compact('classifier_type', 'tableComments'));
    }

    /**
     * Update the specified classifier type.
     *
     * @param Request $request Updated classifier type data
     * @param ClassifierType $classifierType The classifier type to update
     * @return ClassifierType The updated classifier type
     */
    public function update(Request $request, ClassifierType $classifierType)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $classifierType->update($request->except(['_token', '_method']));

        return $classifierType;
    }

    /**
     * Remove the specified classifier type from storage.
     *
     * @param ClassifierType $classifierType The classifier type to delete
     * @return ClassifierType The deleted classifier type
     */
    public function destroy(ClassifierType $classifierType)
    {
        $classifierType->delete();

        return $classifierType;
    }
}
