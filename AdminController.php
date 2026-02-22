<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authorized_staff;

class AdminController extends Controller
{
    public function authorizeStaff(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9.]+@aubg\.edu$/|unique:authorised_staff',
            'role'  => 'required|string', 
        ]);

        // Save to Db
        Authorized_staff::create([
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        // Go back with a success msg
        return redirect()->back()->with('success', 'Staff member added successfully!');
    }
}