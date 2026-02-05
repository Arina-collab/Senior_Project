<?
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Student user for authentication
        $this->studentUser = User::factory()->create(['role' => 'Student']);
    }

    /** @test */
    public function student_views_profile_setup()
    {
        // Seed a club to ensure it shows up
        DB::table('available_clubs')->insert(['club_name' => 'Computer Science Club']);

        $response = $this->actingAs($this->studentUser)
                         ->get(route('profile.setup'));

        $response->assertStatus(200);
        $response->assertViewHas('major_list');
        $response->assertViewHas('club_list', function($clubs) {
            return $clubs->contains('Computer Science Club');
        });
    }

    /** @test */
    public function student_creates_profile_with_valid_data()
    {
        $profileData = [
            'first_name' => 'Ivan',
            'second_name' => 'Ivanov',
            'graduation_year' => now()->year + 2,
            'majors' => ['Computer Science', 'Mathematics'],
            'phone' => '0888123456',
            'bio' => 'Hello, I am a student.',
            'clubs' => ['Basketball'],
            'custom_club' => 'Startup Club'
        ];

        $response = $this->actingAs($this->studentUser)
                         ->post(route('profile.store'), $profileData);

        $response->assertRedirect(route('profile.setup'));
        $response->assertSessionHas('msg', 'Profile updated successfully!');

        // Check if profile was saved
        $this->assertDatabaseHas('students', [
            'user_id' => $this->studentUser->id,
            'first_name' => 'Ivan',
            'major' => 'Computer Science, Mathematics'
        ]);

        // Check if custom club was added to available_clubs
        $this->assertDatabaseHas('available_clubs', ['club_name' => 'Startup Club']);
    }

    /** @test */
    public function profile_fails_if_grad_year_is_in_the_past()
    {
        $currentYear = now()->year;
        $response = $this->actingAs($this->studentUser)
                         ->post(route('profile.store'), [
            'graduation_year' => $currentYear - 1, // Invalid
        ]);

        $response->assertSessionHasErrors(['graduation_year']);
        // Error message
        $this->assertEquals(
            "Graduation year cannot be earlier than $currentYear.", 
            session('errors')->get('graduation_year')[0]
        );
    }

    /** @test */
    public function profile_fails_if_more_than_three_majors()
    {
        $response = $this->actingAs($this->studentUser)
                         ->post(route('profile.store'), [
            'majors' => ['CS', 'Math', 'Econ', 'History'],
        ]);

        $response->assertSessionHasErrors(['majors']);
        $this->assertEquals(
            "Please select between 1 and 3 majors.", 
            session('errors')->get('majors')[0]
        );
    }

    /** @test */
    public function names_cannot_contain_special_characters_or_numbers()
    {
        $response = $this->actingAs($this->studentUser)
                         ->post(route('profile.store'), [
            'first_name' => 'Ivan123!',
        ]);

        $response->assertSessionHasErrors(['first_name']);
    }

    /** @test */
    public function bio_content_is_stripped_of_html_tags()
    {
        $this->actingAs($this->studentUser)->post(route('profile.store'), [
            'first_name' => 'Ivan',
            'second_name' => 'Ivanov',
            'graduation_year' => now()->year,
            'majors' => ['Computer Science'],
            'phone' => '123456789',
            'bio' => '<b>Bold Text</b> <script>alert("hack")</script>',
        ]);

        $this->assertDatabaseHas('students', [
            'user_id' => $this->studentUser->id,
            'bio' => 'Bold Text alert("hack")' // Tags should be gone
        ]);
    }

    /** @test */
    public function guests_cannot_access_profile_routes()
    {
        $this->get(route('profile.setup'))->assertRedirect('/login');
        $this->post(route('profile.store'), [])->assertRedirect('/login');
    }
}