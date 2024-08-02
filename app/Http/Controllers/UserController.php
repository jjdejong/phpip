<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('readonly');
        $user = new User;
        if ($request->filled('Name')) {
            $user = $user->where('name', 'like', $request->Name . '%');
        }
        $userslist = $user->with('company')->orderby('name')->paginate(21);
        $userslist->appends($request->input())->links();

        return view('user.index', compact('userslist'));
    }

    public function create()
    {
        Gate::authorize('admin');
        $table = new \App\Models\Actor;
        $userComments = $table->getTableComments('actor');

        return view('user.create', compact('userComments'));
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'name' => 'required|unique:actor|max:100',
            'login' => 'required|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'email' => 'required|email',
            'default_role' => 'required',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return User::create($request->except(['_token', '_method', 'password_confirmation']));
    }

    public function show(User $user)
    {
        Gate::authorize('readonly');
        $userInfo = $user->load(['company:id,name', 'roleInfo']);
        $table = new \App\Models\Actor;
        $userComments = $table->getTableComments('actor');

        return view('user.show', compact('userInfo', 'userComments'));
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('admin');
        $request->validate([
            'login' => 'sometimes|required|unique:users',
            'password' => 'sometimes|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^a-zA-Z0-9]/',
            'email' => 'sometimes|required|email',
            'default_role' => 'sometimes|required',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        if ($request->filled('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        $user->update($request->except(['_token', '_method']));

        return $user;
    }

    public function destroy(User $user)
    {
        Gate::authorize('admin');
        $user->delete();

        return $user;
    }
}
