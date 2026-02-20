<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_student_with_aubg_email()
    {
        $response = $this->post(route('signup_btn'), [
            'email' => 'test.student@aubg.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test.student@aubg.edu',
            'role' => 'Student',
        ]);
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('msg', 'Registration successful! Please log in.');
    }

    /** @test */
    public function fail_with_non_aubg_email()
    {
        $response = $this->post(route('signup_btn'), [
            'email' => 'test@gmail.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function staff_if_in_authorized_table()
    {
        DB::table('authorized_staff')->insert([
            'email' => 'testcareer@aubg.edu',
            'role' => 'Career Center',
        ]);

        $this->post(route('signup_btn'), [
            'email' => 'testcareer@aubg.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testcareer@aubg.edu',
            'role' => 'Career Center',
        ]);
    }

    /** @test */
    public function login_redirect_student_to_profile_setup()
    {
        $user = User::create([
            'email' => 'student@aubg.edu',
            'password' => Hash::make('Password123!'),
            'role' => 'Student',
        ]);

        $response = $this->post(route('login_btn'), [
            'email' => 'student@aubg.edu',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('profile.setup'));
    }

    /** @test */
    public function login_redirect_career_center()
    {
        $user = User::create([
            'email' => 'career@aubg.edu',
            'password' => Hash::make('Password123!'),
            'role' => 'Career Center',
        ]);

        $response = $this->post(route('login_btn'), [
            'email' => 'career@aubg.edu',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('career.dashboard'));
        $response->assertSessionHas('msg', 'Welcome back to the Career Center!');
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout_btn'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }
}