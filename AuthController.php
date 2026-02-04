<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

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
        
        // Check the authorized table
        $authorized = DB::table('authorized_staff')
        ->where('email', $request->email)
        ->first();

        // Assign role
        $finalRole = $authorized ? $authorized->role : 'Student';

        //Save new user to DB
        User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => $finalRole,
        ]);
        //Send back + msg
        return redirect()->route('home')->with('msg', 'Registration successful! Please log in.');
    }

    // Login
    public function login(Request $request)
    {
    // Validate
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();

        // redirect students
        if ($user->role === 'Student') {
            return redirect()->route('profile.setup');
        }

        // redirect career center
        if ($user->role === 'Career Center') {
            return redirect()->route('career.dashboard')->with('msg', 'Welcome back to the Career Center!');
        }

        // Other staff roles for later
        return redirect()->route('home')->with('msg', 'Welcome back, Staff!');
    }

    // Error handling
    return back()->with('msg', 'Invalid email or password.')->onlyInput('email');
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