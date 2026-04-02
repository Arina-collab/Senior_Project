<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student_Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentIssuesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function report_issue_page_is_accessible()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('issue.create'));

        $response->assertStatus(200);
        $response->assertViewIs('student.student_issues');
        $response->assertSee('Report a Technical Issue');
    }

    /** @test */
    public function submit_an_issue_with_screenshot()
    {
        Storage::fake('public');

        $screenshot = UploadedFile::fake()->image('bug.png');
        $comment = 'This is a detailed description of the bug that is longer than twenty characters.';

        $response = $this->actingAs($this->user)
                         ->post(route('issue.store'), [
                             'comment' => $comment,
                             'screenshot' => $screenshot,
                         ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('student_issues', [
            'user_id' => $this->user->id,
            'comment' => $comment,
            'status'  => 'open',
        ]);

        $issue = Student_Issue::first();
        Storage::disk('public')->assertExists($issue->screenshot_path);
    }

    /** @test */
    public function fail_comment_too_short()
    {
        $response = $this->actingAs($this->user)
                         ->from(route('issue.create'))
                         ->post(route('issue.store'), [
                             'comment' => 'Too short',
                         ]);

        $response->assertSessionHasErrors('comment');
        $this->assertDatabaseCount('student_issues', 0);
    }

    /** @test */
    public function success_get_browser_info()
    {
        $response = $this->actingAs($this->user)
                         ->withHeaders(['User-Agent' => 'TestBrowser/1.0'])
                         ->post(route('issue.store'), [
                             'comment' => 'Standard length comment for browser info test.',
                         ]);

        $this->assertDatabaseHas('student_issues', [
            'browser_info' => 'TestBrowser/1.0',
        ]);
    }
}