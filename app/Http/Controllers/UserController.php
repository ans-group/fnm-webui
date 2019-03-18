<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use \App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $users = \App\User::all();
        return view('user.index')->with('users', $users);
    }

    public function create() {
        return view('user.create');
    }

    public function store(Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'required|boolean',
            'admin' => 'required|boolean',
            'password' => 'required|confirmed|min:6',
            'notify' => 'required|boolean',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        return redirect()->route('users.edit', $user)->with('success', 'User created.');
    }

    public function show(User $user) {
        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user) {
        return view('user.edit')->with('user', $user);
    }

    public function update(User $user, Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'required|boolean',
            'admin' => 'required|boolean',
            'password' => 'sometimes|confirmed',
            'notify' => 'required|boolean',
        ]);

        if(empty($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);
        return back()->with('success', "User updated.");
    }

    public function toggle (User $user) {
        $user->active = !$user->active;
        $user->save();

        $state = $user->active ? 'enabled' : 'disabled';

        return back()->with('success', 'User '.$user->name.' set to '.$state.'.');
    }

    public function password(User $user) {
        return view('user.password')->with('user', $user);
    }

    public function updatePassword(Request $request) {
        $save = $request->all();
        $user = Auth::user();
        if( !empty($save['original_password']) || !empty($save['password']) || !empty($save['password2']) ) {
            if( !Hash::check($save['original_password'], $user->password) ) {
                return back()->with('status', "Invalid current password!");
            }

            if ($save['password'] !== $save['password2']) {
                return back()->with('status', "Passwords did not match!");
            }

            $save['password'] = bcrypt($save['password']);
            unset($save['original_password']);
            unset($save['password2']);
            $user->update($save);
            return back()->with('status', "Passwords updated!");
        }
        return back()->with('status', "Passwords were empty!");
    }

    public function delete(User $user) {
        if($user->active) {
            return back();
        }
        $user->delete();
        return redirect()->route('users.index')->with('status', "User deleted!");
    }

}
