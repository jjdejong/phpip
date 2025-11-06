<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages matter categories.
 *
 * Categories classify matters into types like Patent, Trademark, Design, etc.
 * Controls matter display grouping and reference number prefixes.
 */
class CategoryController extends Controller
{
    /**
     * Display a list of categories with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Category = $request->input('Category');
        $category = Category::query();

        if (! is_null($Code)) {
            $category = $category->whereLike('code', $Code.'%');
        }

        if (! is_null($Category)) {
            $category = $category->whereJsonLike('category', $Category);
        }

        $categories = $category->get();

        if ($request->wantsJson()) {
            return response()->json($categories);
        }

        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = new Category;
        $tableComments = $category->getTableComments();
        return view('category.create', compact('tableComments'));
    }

    /**
     * Store a newly created category.
     *
     * @param Request $request Category data including code, category name, and display_with
     * @return Category The created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:matter_category|max:5',
            'category' => 'required|max:45',
            'display_with' => 'required',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return Category::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified category.
     *
     * @param Category $category The category to display
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $tableComments = $category->getTableComments();
        $category->load(['displayWithInfo:code,category']);

        return view('category.show', compact('category', 'tableComments'));
    }

    /**
     * Update the specified category.
     *
     * @param Request $request Updated category data
     * @param Category $category The category to update
     * @return Category The updated category
     */
    public function update(Request $request, Category $category)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $category->update($request->except(['_token', '_method']));

        return $category;
    }

    /**
     * Remove the specified category from storage.
     *
     * @param Category $category The category to delete
     * @return Category The deleted category
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $category;
    }
}
