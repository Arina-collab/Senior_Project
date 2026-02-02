<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return view('Home');
})->name('home');

// Auth Routes...
Route::post('/signup', [AuthController::class, 'signup'])->name('signup_btn');
Route::post('/login', [AuthController::class, 'login'])->name('login_btn');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout_btn');

// 2. Profile Routes inside the Middleware
Route::middleware(['auth'])->group(function () {
    // Use the array syntax [Controller::class, 'method']
    Route::get('/profile/setup', [ProfileController::class, 'show'])->name('profile.setup');
    Route::post('/profile/setup', [ProfileController::class, 'store'])->name('complete_profile_btn');
});

// Forgot Password Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

//Pass reset - the link in the email will point here
Route::get('reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

