<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    //Display
    public function index()
    {
        // Fetch posts
        $my_posts = Opportunity::where('posted_by', Auth::id())
            ->orderBy('is_priority', 'desc')
            ->latest()
            ->get();

        // Get categories
        $categories = \DB::table('opportunity_categories')->orderBy('name', 'asc')->get();            
        
        return view('career.career', compact('my_posts', 'categories'));
    }

    //Store a new opportunity
    public function store(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'type'        => 'required|string',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'deadline'    => 'nullable|date',
            'is_priority' => 'nullable',
        ]);

        // LOGIC: Save the category if it's new
        \DB::table('opportunity_categories')->updateOrInsert(
        ['name' => trim($request->category)],
        ['created_at' => now()]);

        // Assign 'posted_by'
        $validated['posted_by'] = Auth::id();

        // Is_priority logic 
        $validated['is_priority'] = $request->has('is_priority') ? 1 : 0;

        // Mass Assignment to create the record
        Opportunity::create($validated);

        return back()->with('msg', 'Opportunity published successfully!');
    }
}