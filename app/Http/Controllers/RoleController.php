<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Name = $request->input('Name');
        $role = Role::query();
        
        if (! is_null($Code)) {
            $role = $role->whereLike('code', $Code.'%');
        }

        if (! is_null($Name)) {
            $role = $role->whereJsonLike('name', $Name);
        }

        $roles = $role->get();

        if ($request->wantsJson()) {
            return response()->json($roles);
        }

        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $table = new Role;
        $tableComments = $table->getTableComments();

        return view('role.create', compact('tableComments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:actor_role|max:5',
            'name' => 'required|max:45',
            'display_order' => 'numeric|nullable',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return Role::create($request->except(['_token', '_method']));
    }

    public function show(Role $role)
    {
        $tableComments = $role->getTableComments();
        $role->get();

        return view('role.show', compact('role', 'tableComments'));
    }

    public function update(Request $request, Role $role)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $role->update($request->except(['_token', '_method']));

        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return $role;
    }
}
