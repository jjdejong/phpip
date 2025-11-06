<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages actor roles in the system.
 *
 * Roles define the type of relationship an actor has with a matter
 * (e.g., Applicant, Inventor, Agent, Contact). Used in matter-actor
 * relationships and default actor configurations.
 */
class RoleController extends Controller
{
    /**
     * Display a list of roles with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
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

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Role;
        $tableComments = $table->getTableComments();

        return view('role.create', compact('tableComments'));
    }

    /**
     * Store a newly created role.
     *
     * @param Request $request Role data including code, name, and display_order
     * @return Role The created role
     */
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

    /**
     * Display the specified role.
     *
     * @param Role $role The role to display
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $tableComments = $role->getTableComments();
        $role->get();

        return view('role.show', compact('role', 'tableComments'));
    }

    /**
     * Update the specified role.
     *
     * @param Request $request Updated role data
     * @param Role $role The role to update
     * @return Role The updated role
     */
    public function update(Request $request, Role $role)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $role->update($request->except(['_token', '_method']));

        return $role;
    }

    /**
     * Remove the specified role from storage.
     *
     * @param Role $role The role to delete
     * @return Role The deleted role
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return $role;
    }
}
