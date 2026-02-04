<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Career Center Dashboard | AUBG Portal</title>
    <link rel="stylesheet" href="{{ asset('css/Career_profile.css') }}">
</head>
<body>

<div class="sidebar">
    <h2>AUBG Portal</h2>
    <nav>
        <a href="{{ route('career.dashboard') }}">📊 Add an opportunity</a>
        <a href="#">📋 All Postings</a>
        <a href="#">🎓 Student Directory</a>
        <hr>
        <form action="{{ route('logout_btn') }}" method="POST">
            @csrf
            <button type="submit" style="background:none; color:white; border:none; cursor:pointer; font-size:16px;">Log Out</button>
        </form>
    </nav>
</div>

<div class="main-content">
    <div class="welcome-header">
        <h1>Career Center Management</h1>
        <p>Use this form to post new job opportunities and internships for AUBG students.</p>
        
        @if(session('msg'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('msg') }}
            </div>
        @endif
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px;">
        <h2 style="margin-top:0;">Create New Opportunity</h2>
        <form action="{{ route('opportunities.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" placeholder="e.g. Junior Data Analyst" required>
            </div>

            <div style="display:flex; gap:20px;">
                <div class="form-group" style="flex:1;">
                    <label>Category</label>
                    <input type="text" 
                        name="category" 
                        list="category-list" 
                        placeholder="Search or type new category..." 
                        required 
                        autocomplete="off">
    
            <datalist id="category-list">
                @foreach($categories as $cat)
                <option value="{{ $cat->name }}">
                @endforeach
            </datalist>
            <small style="color: #666;">Tip: Typing a new name will save it for future use.</small>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Job Type</label>
                    <select name="type" required>
                        <option value="Internship">Internship</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Volunteering">Volunteering</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:20px;">
                <div class="form-group" style="flex:1;">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. Sofia, Remote, or Campus" value="Sofia">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Application Deadline</label>
                    <input type="date" name="deadline">
                </div>
            </div>

            <div class="form-group">
                <label>Job Description</label>
                <textarea name="description" rows="6" placeholder="Describe the role, requirements, and how to apply..." required></textarea>
            </div>

            <div class="priority-box">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_priority" value="1" style="width:20px; height:20px;">
                    <span style="font-weight: bold; color: #b38f00;">🌟 Mark as AUBG Priority Hiring</span>
                </label>
                <p style="font-size: 13px; color: #666; margin: 5px 0 0 30px;">
                    This will highlight the post and keep it at the top of the student feed.
                </p>
            </div>

            <button type="submit" style="background: #003366; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;">
                Publish Opportunity
            </button>
        </form>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">
</div>

</body>
</html>