<?php

namespace App\Http\Controllers;

use App\Role;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Code  = $request->input('Code');
        $Name = $request->input('Name');
        $role = Role::query() ;
        if (!is_null($Code)) {
            $role = $role->where('code', 'like', $Code.'%');
        }
        if (!is_null($Name)) {
            $name = $role->where('name', 'like', $Name.'%');
        }

        $roles = $role->get();
        return view('role.index', compact('roles'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('actor_role');
        return view('role.create', compact('tableComments'));
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
            'code' => 'required|unique:actor_role|max:5',
            'name' => 'required|max:45',
            'display_order' => 'numeric|nullable'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return Role::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('actor_role');
        $role->get();
        return view('role.show', compact('role', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $role->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Role updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['success' => 'Role deleted']);
    }
}
