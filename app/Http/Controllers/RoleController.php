<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Role;
use App\Models\Translations\ActorRoleTranslation;
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
            $role = $role->where('code', 'like', $Code.'%');
        }
        if (! is_null($Name)) {
            $name = $role->where('name', 'like', $Name.'%');
        }

        $roles = $role->get();

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
            'notes' => 'max:160'
        ]);
        
        $request->merge(['creator' => Auth::user()->login]);
        
        $role = Role::create($request->except(['_token', '_method']));
        
        $translatableFields = ['name', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            ActorRoleTranslation::create([
                'code' => $role->code,
                'locale' => $locale,
                ...$translations
            ]);
        }

        return response()->json(['redirect' => route('role.index')]);
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
        
        $translatableFields = ['name', 'notes'];
        $locale = Auth::user()->getLanguage();
        $baseLanguage = explode('_', $locale)[0];
        
        if ($baseLanguage === 'en') {
            $locale = 'en';
        }
        
        $translations = array_intersect_key($request->all(), array_flip($translatableFields));
        if (!empty(array_filter($translations))) {
            ActorRoleTranslation::updateOrCreate(
                [
                    'code' => $role->code,
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
            $role->update($nonTranslatableData);
        }
        
        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return $role;
    }
}
