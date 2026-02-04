<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | AUBG Portal</title>
    <link rel="stylesheet" href="{{ asset('css/Profile.css') }}">
</head>
<body>

<div class="sidebar">
    <h2>AUBG Portal</h2>
    <p>Password Recovery</p>
    <nav>
        <a href="{{ route('home') }}">🏠 Return to Login</a>
    </nav>
</div>

<div class="main-content">
    <div class="welcome-header">
        <h1>Forgot Your Password?</h1>
        <p>No worries! Enter your AUBG email and we'll send you a reset link.</p>
        
        @if (session('status'))
            <p class="msg" style="color: green; background: #e6ffed;">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="msg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>

    <form action="{{ route('password.email') }}" method="POST" style="max-width: 400px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        @csrf
        <label>AUBG Email Address</label>
        <input type="email" name="email" placeholder="e.g. ab123@aubg.edu" required>
        
        <button type="submit" style="background: #003366; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px;">
            Send Reset Link
        </button>
    </form>
</div>
</body>
</html>