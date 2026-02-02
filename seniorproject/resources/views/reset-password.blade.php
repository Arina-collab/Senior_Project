<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | AUBG Portal</title>
    <link rel="stylesheet" href="{{ asset('css/Profile.css') }}">
</head>
<body>

<div class="sidebar">
    <h2>AUBG Portal</h2>
    <p>Security Update</p>
    <nav>
        <a href="{{ route('home') }}">🏠 Cancel</a>
    </nav>
</div>

<div class="main-content">
    <div class="welcome-header">
        <h1>Set New Password</h1>
        <p>Please choose a strong password to secure your account.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST" style="max-width: 400px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label>Email Address</label>
        <input type="email" name="email" value="{{ request()->email }}" required readonly style="background: #f9f9f9;">

        <label>New Password</label>
        <input type="password" name="password" placeholder="Min. 6 characters" required>

        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" placeholder="Repeat new password" required>

        <button type="submit" style="background: #003366; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px;">
            Update Password
        </button>
    </form>
</div>
</body>
</html>