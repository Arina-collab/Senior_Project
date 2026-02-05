<?
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
    public function signup_with_valid_aubg_email()
    {
        $response = $this->post('/signup', [
            'email' => 'student123@aubg.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'student123@aubg.edu',
            'role' => 'Student'
        ]);
    }

    /** @test */
    public function career_center_if_authorized_staff()
    {
        // Seed the authorized_staff table
        DB::table('authorized_staff')->insert([
            'email' => 'staff@aubg.edu',
            'role' => 'Career Center'
        ]);

        $this->post('/signup', [
            'email' => 'staff@aubg.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'staff@aubg.edu',
            'role' => 'Career Center'
        ]);
    }

    /** @test */
    public function signup_fails_with_invalid_email()
    {
        $response = $this->post('/signup', [
            'email' => 'invalid@gmail.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function student_redirected_to_profile_setup_after_login()
    {
        $user = User::create([
            'email' => 'student123@aubg.edu',
            'password' => Hash::make('Password123!'),
            'role' => 'Student'
        ]);

        $response = $this->post('/login', [
            'email' => 'student123@aubg.edu',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('profile.setup'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function career_center_redirected_to_dashboard_after_login()
    {
        User::create([
            'email' => 'staff@aubg.edu',
            'password' => Hash::make('Password123!'),
            'role' => 'Career Center'
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@aubg.edu',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('career.dashboard'));
        $response->assertSessionHas('msg', 'Welcome back to the Career Center!');
    }

    /** @test */
    public function login_fails_with_incorrect_credentials()
    {
        User::create([
            'email' => 'user@aubg.edu',
            'password' => Hash::make('CorrectPassword123!'),
            'role' => 'Student'
        ]);

        $response = $this->post('/login', [
            'email' => 'user@aubg.edu',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHas('msg', 'Invalid email or password.');
        $this->assertGuest();
    }
}