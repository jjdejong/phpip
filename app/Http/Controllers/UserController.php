<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Actor;
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

        $query = $user->with('company')->orderby('name');

        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        $userslist = $query->paginate(21);
        $userslist->appends($request->input())->links();

        return view('user.index', compact('userslist'));
    }

    public function create()
    {
        Gate::authorize('admin');
        $table = new Actor;
        $userComments = $table->getTableComments();

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
        $table = new Actor;
        $userComments = $table->getTableComments();

        return view('user.show', compact('userInfo', 'userComments'));
    }

    public function edit(User $user)
    {
        Gate::authorize('admin');
        $table = new Actor;
        $userComments = $table->getTableComments();

        return view('user.edit', compact('user', 'userComments'));
    }
    
    public function profile()
    {
        $userInfo = Auth::user()->load(['company:id,name', 'roleInfo']);
        $table = new Actor;
        $userComments = $table->getTableComments();

        // Add a flag to indicate this is the profile view
        $isProfileView = true;

        return view('user.profile', compact('userInfo', 'userComments', 'isProfileView'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('admin');
        $request->validate([
            'login' => 'sometimes|required|unique:users',
            'password' => 'sometimes|confirmed|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^a-zA-Z0-9]/',
            'email' => 'sometimes|required|email',
            'default_role' => 'sometimes|required',
            'language' => 'sometimes|required|string|max:5',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        if ($request->filled('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        $user->update($request->except(['_token', '_method']));
        
        // Update locale for the current session if current user is updating their own profile
        if (Auth::id() === $user->id && $request->filled('language')) {
            // Set application locale to the full locale (e.g., 'en_US', 'fr')
            app()->setLocale($request->language);
            
            // Store the locale in session
            session(['locale' => $request->language]);
        }

        return $user;
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'nullable|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^a-zA-Z0-9]/',
            'email' => 'required|email',
            'language' => 'required|string|max:5',
        ]);
        
        $request->merge(['updater' => $user->login]);
        
        $dataToUpdate = [
            'email' => $request->email,
            'language' => $request->language,
            'updater' => $user->login
        ];
        
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }
        
        $user->update($dataToUpdate);
        
        // Update locale for the current session
        if ($request->filled('language')) {
            // Set application locale to the full locale (e.g., 'en_US', 'fr')
            app()->setLocale($request->language);
            
            // Store the locale in session
            session(['locale' => $request->language]);
        }
        
        return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
    }

    public function destroy(User $user)
    {
        Gate::authorize('admin');
        $user->delete();

        return $user;
    }
}
