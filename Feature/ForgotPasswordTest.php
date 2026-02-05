<?
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function send_reset_link_to_valid_user()
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'student123@aubg.edu']);

        $response = $this->post(route('password.email'), [
            'email' => 'student123@aubg.edu',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function fails_if_the_email_is_not_in_the_database()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'stranger@gmail.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /*
        Add the following tmrw:
        Resetting the password with a valid token vs failure
        failure if passwords don't match
        failure if password's too short
     */
}