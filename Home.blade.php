<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AUBG Career Management Portal</title>
    <link rel="stylesheet" href="{{ asset('css/Home.css') }}">
</head>
<body>

<header>
    <h1>Global Paths, AUBG Roots</h1>
    <p>Helping students navigate careers, events, and campus life.</p>
</header>

<div class="container">
    {{-- Display General Messages (Success/Manual Errors) --}}
    @if(session('msg'))
        <div class="msg">{{ session('msg') }}</div>
    @endif

    {{-- Display Validation Errors (like "Invalid AUBG email") --}}
    @if ($errors->any())
        <div class="msg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @auth
        <div class="msg" style="color:green;">
            Welcome, {{ auth()->user()->email }} ({{ auth()->user()->role }})
        </div>
        {{-- Updated to logout_btn route --}}
        <form action="{{ route('logout_btn') }}" method="POST">
            @csrf
            <button type="submit">Log Out</button>
        </form>
    @else
        <section class="auth-section">
            <div class="auth-box">
                <h2>Join the Platform</h2>
                {{-- Updated to signup_btn route --}}
                <form action="{{ route('signup_btn') }}" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="AUBG Email" value="{{ old('email') }}" required>
                    <input type="password" name="password" placeholder="Password" required>
                    {{-- Note: name must be password_confirmation for Laravel's 'confirmed' rule --}}
                    <input type="password" name="password_confirmation" placeholder="Retype password" required>
                    <button type="submit">Get Started</button>
                </form>
            </div>

            <div class="auth-box">
                <h2>Log In</h2>
                {{-- Updated to login_btn route --}}
                <form action="{{ route('login_btn') }}" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div style="text-align: left; margin-top: -5px; margin-bottom: 15px;">
                    <a href="{{ route('password.request') }}" style="font-size: 13px; color: #003366; text-decoration: none;">
                    Forgot password?
                    </a>
                    </div>
                    <button type="submit" style="background:#003366;">Log In</button>
                </form>
            </div>
        </section>
    @endauth
</div>
</body>
</html>