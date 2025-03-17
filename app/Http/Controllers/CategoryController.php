<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Category = $request->input('Category');
        $category = Category::query();
        if (! is_null($Code)) {
            $category = $category->where('code', 'like', $Code.'%');
        }
        if (! is_null($Category)) {
            $category = $category->where('category', 'like', $Category.'%');
        }

        $categories = $category->get();

        return view('category.index', compact('categories'));
    }

    public function create()
    {
        $category = new Category;
        $tableComments = $category->getTableComments();
        return view('category.create', compact('tableComments'));
    }

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

    public function show(Category $category)
    {
        $tableComments = $category->getTableComments();
        $category->load(['displayWithInfo:code,category']);

        return view('category.show', compact('category', 'tableComments'));
    }

    public function update(Request $request, Category $category)
    {
        $request->merge(['updater' => Auth::user()->login]);
        
        // Define which fields are translatable
        $translatableFields = ['category'];
        
        // Process the update, separating translatable fields
        $nonTranslatableData = $category->updateTranslationFields(
            $request->except(['_token', '_method']), 
            $translatableFields
        );
        
        // Update non-translatable fields on the main model if there are any
        if (!empty($nonTranslatableData)) {
            $category->update($nonTranslatableData);
        }
        
        // Make sure we're returning the model with updated translations
        $category->refresh();
        
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $category;
    }
}
