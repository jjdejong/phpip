<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Translations\MatterCategoryTranslation;
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
        
        // Create the category first
        $category = Category::create($request->except(['_token', '_method']));
        
        // Handle translations
        $translatableFields = ['category'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            MatterCategoryTranslation::create([
                'code' => $category->code,
                'locale' => $locale,
                ...$translations
            ]);
        }

        return response()->json(['redirect' => route('category.index')]);
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
        
        $translatableFields = ['category'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            MatterCategoryTranslation::updateOrCreate(
                [
                    'code' => $category->code,
                    'locale' => $locale
                ],
                $translations
            );
        }
        
        $nonTranslatableData = array_diff_key(
            $request->except(['_token', '_method']),
            array_flip($translatableFields)
        );
        
        if (!empty($nonTranslatableData)) {
            $category->update($nonTranslatableData);
        }
        
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $category;
    }
}
