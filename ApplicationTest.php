<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Dummy opportunity to apply to
        DB::table('opportunities')->insert([
            'id' => 1,
            'title' => 'Software Intern',
            'company_name' => 'AUBG Tech',
            'type' => 'Internship',
            'application_link' => 'https://external-job.com/apply',
            'created_at' => now(),
        ]);
    }

    /** @test */
    public function view_apps()
    {
        $user = User::factory()->create();
        
        DB::table('applications')->insert([
            'opportunity_id' => 1,
            'student_id' => $user->id,
            'q1_answer' => 'I am very motivated.',
            'q2_answer' => 'I have great skills.',
            'cv_path' => 'resumes/test.pdf',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('applications.index'));

        $response->assertStatus(200);
        $response->assertViewHas('applications');
        $response->assertSee('Software Intern');
    }

    /** @test */
    public function apply_to_opp()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->from('/opportunity/1')->post(route('applications.apply', ['id' => 1]), [
            'q1_answer' => 'This is a long enough answer for question one.',
            'q2_answer' => 'This is another long enough answer for question two.',
            'cv' => $file,
        ]);

        $response->assertRedirect('/opportunity/1');
        $response->assertSessionHas('success_app');
        
        $this->assertDatabaseHas('applications', [
            'opportunity_id' => 1,
            'student_id' => $user->id,
        ]);

        $application = DB::table('applications')->where('student_id', $user->id)->first();
        Storage::disk('public')->assertExists($application->cv_path);
    }

    /** @test */
    public function cant_apply_twice_to_opp()
    {
        $user = User::factory()->create();
        DB::table('applications')->insert([
            'opportunity_id' => 1,
            'student_id' => $user->id,
            'q1_answer' => 'First try',
            'q2_answer' => 'First try',
            'cv_path' => 'path.pdf',
        ]);

        $response = $this->actingAs($user)->post(route('applications.apply', ['id' => 1]), [
            'q1_answer' => 'Testing now for the second try',
            'q2_answer' => 'Testing now for the second try',
            'cv' => UploadedFile::fake()->create('resume.pdf', 100),
        ]);

        $response->assertSessionHas('error_app', 'You have already applied for this opportunity.');
        $this->assertEquals(1, DB::table('applications')->where('opportunity_id', 1)->count());
    }

    /** @test */
    public function track_external_redirects_and_saves_entry_if_requested()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('applications.track', ['id' => 1]), [
            'track_application' => 'on'
        ]);

        $response->assertRedirect('https://external-job.com/apply');

        $this->assertDatabaseHas('applications', [
            'opportunity_id' => 1,
            'student_id' => $user->id,
            'cv_path' => 'link'
        ]);
    }

    /** @test */
    public function track_external_redirects_if_tracking_not_requested()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('applications.track', ['id' => 1]));

        $response->assertRedirect('https://external-job.com/apply');
    
        $this->assertDatabaseMissing('applications', [
            'opportunity_id' => 1,
            'student_id' => $user->id,
        ]);
    }
}