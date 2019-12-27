<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $Name = $request->Name;
        $user = new User;
        if (!is_null($Name)) {
            $user = $user->where('name', 'like', $Name . '%');
        }
        $users = $user->with('company, role')->orderby('name')->get();
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $table = new Actor;
        //TODO getTableComments is the same as in Rule.php. To render common
        $userComments = $table->getTableComments('actor');
        return view('user.create', compact('actorComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:100',
            'login' => 'required',
            'email' => 'required|email'
            'password' => 'required|confirmed'
        ]);
        return User::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        $userInfo = $user->load(['company:id,name', 'droleInfo']);
        $userComments = $user->getTableComments('actor');
        return view('user.show', compact('userInfo', 'actorComments'));
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

        $user->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'User updated']);
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
