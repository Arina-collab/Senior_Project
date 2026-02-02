<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Registration Logic
    public function signup(Request $request)
    {
        $request->validate([
            //Checks AUBG domain, if there is only 1 user with the email, & pass rules
            'email' => 'required|email|regex:/^[a-zA-Z0-9.]+@aubg\.edu$/|unique:users',
            'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()->symbols()],
        ]);
        //Role assignment
        $role = preg_match("/[0-9]/", $request->email) ? "Student" : "Staff";

        //Save new user to DB
        User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => $role,
        ]);
        //Send back + msg
        return redirect()->route('home')->with('msg', 'Registration successful! Please log in.');
    }

    // Login Logic
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Redirect based on role
            if ($user->role === 'Student') {
                return redirect()->route('profile.setup');
            }
            return redirect()->route('home')->with('msg', 'Welcome back, Staff!');
        }
        return back()->with('msg', 'Invalid email or password.');
    }

    // Logout Logic
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}