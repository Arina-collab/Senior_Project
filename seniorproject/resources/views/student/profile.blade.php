<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | AUBG Portal</title>
    <link rel="stylesheet" href="{{ asset('css/Profile.css') }}">
</head>
<body>

<div class="sidebar">
    <h2>AUBG Portal</h2>
    <p>Welcome, <strong>{{ $student_profile->first_name ?? auth()->user()->email }}</strong></p>
    <nav>
        <a href="#">🏠 Account</a>
        <a href="#">🔔 Notifications</a>
        <a href="#">📢 Opportunities</a>
        <a href="#">📅 Events</a>
        <a href="#">💼 Applications</a>
        <a href="#">📄 Registrations</a>
        <a href="#">✅ Poster Approval</a>
        <hr>
        <form action="{{ route('logout_btn') }}" method="POST">
            @csrf
            <button type="submit" style="background:none; color:white; border:none; cursor:pointer; font-size:16px;">Log Out</button>
        </form>
    </nav>
</div>

<div class="main-content">
    @if (!$student_profile)
        <div class="welcome-header">
            <h1>Complete Your Profile</h1>
            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="msg">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- Display Success/Manual Messages --}}
            @if(session('msg'))
                <p class="msg">{{ session('msg') }}</p>
            @endif
        </div>

        <form action="{{ route('complete_profile_btn') }}" method="POST" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            @csrf
            <label>Full Name</label>
            <div style="display:flex; gap:10px;">
                <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                <input type="text" name="second_name" placeholder="Last Name" value="{{ old('second_name') }}" required>
            </div>

            <label>Graduation Year & Phone</label>
            <input type="text" inputmode="numeric" name="graduation_year" placeholder="Graduation Year" value="{{ old('graduation_year') }}" required>
            <input type="text" name="phone" placeholder="Phone Number" value="{{ old('phone') }}" required>

            <label>Select Majors (Max 3)</label>
            <input type="text" id="majorInput" onkeyup="filterList('majorInput', 'majorList')" placeholder="Search majors...">
            <div id="majorList" class="scroll-box">
                @foreach($major_list as $m)
                    <label class="item">
                        <input type="checkbox" name="majors[]" value="{{ $m }}" {{ is_array(old('majors')) && in_array($m, old('majors')) ? "checked" : "" }}> 
                        {{ $m }}
                    </label>
                @endforeach
            </div>

            <label>Select Clubs</label>
            <input type="text" id="clubInput" onkeyup="filterList('clubInput', 'clubList')" placeholder="Search clubs...">
            <div id="clubList" class="scroll-box">
                @foreach($club_list as $c)
                    <label class="item">
                        <input type="checkbox" name="clubs[]" value="{{ $c }}" {{ is_array(old('clubs')) && in_array($c, old('clubs')) ? "checked" : "" }}> 
                        {{ $c }}
                    </label>
                @endforeach
            </div>
            <input type="text" name="custom_club" placeholder="Other Club (Optional)" value="{{ old('custom_club') }}">

            <label>Bio</label>
            <textarea name="bio" id="bio" rows="4" maxlength="1500" onkeyup="countChars(this)">{{ old('bio') }}</textarea>
            <p id="charNum" style="font-size: 12px; color: #666;">0/1500 characters</p>

            <button type="submit">Save Profile & Continue</button>
        </form>

    @else
        <div class="welcome-header">
            <h1>Global Paths, AUBG Roots</h1>
            <p>Welcome back, <strong>{{ $student_profile->first_name }}</strong>!</p>
            <p>Major(s): {{ $student_profile->major }}</p>
        </div>

        <div class="opportunity-list">
            <h2>Recommended for You</h2>
            @forelse ($postings as $post)
                <div class="card">
                    <h3>{{ $post->title }}</h3>
                    <span class="badge">{{ $post->type }}</span>
                    <p>{{ Str::limit($post->description, 150) }}</p>
                    <button style="background:#003366; color:white; border:none; padding:8px 15px; border-radius:4px; cursor:pointer;">View Details</button>
                </div>
            @empty
                <p>No opportunities available right now.</p>
            @endforelse
        </div>
    @endif
</div>

<script>
function countChars(obj){
    document.getElementById("charNum").innerHTML = obj.value.length + "/1500 characters";
}

function filterList(inputId, listId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toUpperCase();
    var container = document.getElementById(listId);
    var labels = container.getElementsByClassName("item");

    for (var i = 0; i < labels.length; i++) {
        var textValue = labels[i].textContent || labels[i].innerText;
        labels[i].style.display = (textValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
    }
}
</script>
</body>
</html>