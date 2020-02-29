<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = new User;
        if ($request->filled('Name')) {
            $user = $user->where('name', 'like', $request->Name . '%');
        }
        $userslist = $user->with('company')->orderby('name')->paginate(21);
        $userslist->appends($request->input())->links();
        return view('user.index', compact('userslist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $table = new \App\Actor;
        $userComments = $table->getTableComments('actor');
        return view('user.create', compact('userComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:actor|max:100',
            'login' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^a-zA-Z0-9]/',
            'email' => 'required|email',
            'default_role' => 'required'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return User::create($request->except(['_token', '_method', 'password_confirmation']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        $userInfo = $user->load(['company:id,name', 'roleInfo']);
        $table = new \App\Actor;
        $userComments = $table->getTableComments('actor');
        return view('user.show', compact('userInfo', 'userComments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        $request->validate([
            'login' => 'sometimes|required|unique:users',
            'password' => 'sometimes|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^a-zA-Z0-9]/',
            'email' => 'sometimes|required|email',
            'default_role' => 'sometimes|required'
        ]);
        $request->merge([ 'updater' => Auth::user()->login ]);
        $user->update($request->except(['_token', '_method']));
        return response()->json(['success' => '1']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $user->delete();
        return response()->json(['success' => 'User deleted']);
    }

}
