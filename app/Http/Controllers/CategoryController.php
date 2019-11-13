<?php

namespace App\Http\Controllers;

use App\Category;
use App\Actor;
use Illuminate\Http\Request;
use Response;

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
            'code' => 'required|unique:event_name|max:5',
            'category' => 'required|max:45',
            'display_with' => 'required'
        ]);
        return Category::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($n)
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('matter_category');
        $categoryInfo = Category::with(['displayWithInfo:code,category'])->find($n);
        return view('category.show', compact('categoryInfo', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        $category = Category::find($code);
        $category->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Category updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['success' => 'Category deleted']);
    }
}
