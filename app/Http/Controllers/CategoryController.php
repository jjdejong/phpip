<?php

namespace App\Http\Controllers;

use App\Category;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Code  = $request->input('Code');
        $Category = $request->input('Category');
        $category = Category::query() ;
        if (!is_null($Code)) {
            $category = $category->where('code', 'like', $Code.'%');
        }
        if (!is_null($Category)) {
            $category = $category->where('category', 'like', $Category.'%');
        }

        $categories = $category->get();
        return view('category.index', compact('categories'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('matter_category');
        return view('category.create', compact('tableComments'));
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
            'code' => 'required|unique:matter_category|max:5',
            'category' => 'required|max:45',
            'display_with' => 'required'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return Category::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('matter_category');
        $category->load(['displayWithInfo:code,category']);
        return view('category.show', compact('category', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $category->update($request->except(['_token', '_method']));
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $category;
    }
}
