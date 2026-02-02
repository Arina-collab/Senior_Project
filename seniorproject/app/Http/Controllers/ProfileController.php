<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class ProfileController extends Controller
{
    private $major_list = [
        "Business Administration", "Computer Science", "Economics", "European Studies", 
        "History and Civilizations", "Information Systems", "Journalism and Mass Communication", 
        "Mathematics", "Political Science and International Relations", "Film and Creative Media", 
        "Literature", "Modern Languages and Cultures", "Physics", "Psychology", "Self-Designed Major", "Undeclared"
    ];

    public function show()
    {
        $user = Auth::user();
        
        // Check if student profile already exists
        $student_profile = DB::table('students')->where('user_id', $user->id)->first();

        // Fetch clubs for the form (even if profile exists, we might need them for editing later)
        $club_list = DB::table('available_clubs')->orderBy('club_name', 'asc')->pluck('club_name');

        return view('student.profile', [
            'major_list' => $this->major_list,
            'club_list' => $club_list,
            'student_profile' => $student_profile,
            'current_year' => now()->year,
            'postings' => [] // Placeholder for dashboard opportunities
        ]);
    }

    public function store(Request $request)
    {
        $current_year = now()->year;
        //Validation
        $validated = $request->validate([
            'first_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'second_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'graduation_year' => "required|integer|regex:/^20[0-9]{2}$/|min:$current_year",
            'majors' => 'required|array|min:1|max:3',
            'phone' => 'required|digits_between:7,15',
            'bio' => 'nullable|string|max:1500',
            'custom_club' => 'nullable|regex:/^[a-zA-Z\s]+$/',
        ], [
            'graduation_year.min' => "Graduation year cannot be earlier than $current_year.",
            'majors.max' => "Please select between 1 and 3 majors.",
        ]);

        $selected_clubs = $request->input('clubs', []);

        if ($request->filled('custom_club')) {
            $custom_club = trim($request->custom_club);
            $selected_clubs[] = $custom_club;
            DB::table('available_clubs')->updateOrInsert(['club_name' => $custom_club]);
        }

        //Prevent duplicate profiles if they submit twice
        DB::table('students')->updateOrInsert(
            ['user_id' => Auth::id()], // Search criteria
            [
                'first_name' => $validated['first_name'],
                'second_name' => $validated['second_name'],
                'phone' => $validated['phone'],
                'graduation_year' => $validated['graduation_year'],
                'major' => implode(", ", $validated['majors']),
                'club' => implode(", ", $selected_clubs),
                'bio' => strip_tags($validated['bio']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Redirect to the same page which will now show the dashboard view
        return redirect()->route('profile.setup')->with('msg', 'Profile updated successfully!');
    }
}