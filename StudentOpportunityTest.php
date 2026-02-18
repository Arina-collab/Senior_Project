<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Opportunity;
use App\Models\Application;
use App\Mail\OpportunityApplicationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentOpportunityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function filter_opps_by_keyword()
    {
        Opportunity::factory()->create(['title' => 'Laravel Developer']);
        Opportunity::factory()->create(['title' => 'React Designer']);

        $response = $this->get(route('student.opportunities.index', ['keyword' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Developer');
        $response->assertDontSee('React Designer');
    }

    /** @test */
    public function error_when_opp_does_not_exist()
    {
        $response = $this->get('/student/opportunities/999');
        $response->assertStatus(404);
    }

    /** @test */
    public function warn_if_no_hr_email_exists()
    {
        $opportunity = Opportunity::factory()->create([
            'hr_email' => null,
            'application_link' => 'https://external-site.com'
        ]);

        $response = $this->post(route('student.opportunities.apply', $opportunity->id));

        $response->assertStatus(200);
        $response->assertViewIs('student.redirection_warning');
        $response->assertViewHas('link', 'https://external-site.com');
    }

    /** @test */
    public function send_email_with_valid_input()
    {
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create(['hr_email' => 'hr@company.com']);
        $file = UploadedFile::fake()->create('resume.pdf', 500);

        $formData = [
            'q1_answer' => 'My experience is vast.',
            'q2_answer' => 'I am very motivated.',
            'cv' => $file,
        ];

        $response = $this->actingAs($user)
                         ->post(route('student.opportunities.apply', $opportunity->id), $formData);

        $this->assertDatabaseHas('applications', [
            'opportunity_id' => $opportunity->id,
            'student_id' => $user->id,
            'q1_answer' => 'My experience is vast.'
        ]);

        Storage::disk('public')->assertExists('cvs/' . $file->hashName());

        Mail::assertSent(OpportunityApplicationMail::class, function ($mail) use ($opportunity) {
            return $mail->hasTo($opportunity->hr_email);
        });

        $response->assertSessionHas('success_app');
    }

    /** @test */
    public function fail_if_applied_to_non_existent_opp()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post('/student/opportunities/999/apply', [
                             'q1_answer' => 'Test'
                         ]);

        $response->assertStatus(404);
    }
}