<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student_Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminIssuesControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function view_issues_page_with_data()
    {
        $user = User::factory()->create(['email' => 'student@aubg.edu']);
        $issue = Student_Issue::factory()->create([
            'user_id' => $user->id,
            'comment' => 'The sidebar is overlapping on mobile',
            'status'  => 'open'
        ]);

        $response = $this->actingAs($user)
                         ->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Bug Reports');
        $response->assertSee('student@aubg.edu');   
        $response->assertSee('The sidebar is overlapping on mobile');
        $response->assertViewHas('issues');
    }

    /** @test */
    public function update_issue_status()
    {
        $admin = User::factory()->create();
        $issue = Student_Issue::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)
                         ->from(route('admin.index'))
                         ->patch(route('admin.update', $issue->id), [
                             'status' => 'in_progress'
                         ]);

        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas('msg', "Issue #{$issue->id} updated to in_progress!");
        $this->assertDatabaseHas('student_issues', [
            'id' => $issue->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function fail_update_with_invalid_input()
    {
        $admin = User::factory()->create();
        $issue = Student_Issue::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)
                         ->from(route('admin.index'))
                         ->patch(route('admin.update', $issue->id), [
                             'status' => 'invalid_status_value'
                         ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('open', $issue->fresh()->status);
    }

    /** @test */
    public function delete_issue_and_photo()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create();
        $filePath = 'issues/bug_report_1.png';
        Storage::disk('public')->put($filePath, 'fake_image_content');

        $issue = Student_Issue::factory()->create([
            'screenshot_path' => $filePath
        ]);

        $response = $this->actingAs($admin)
                         ->from(route('admin.index'))
                         ->delete(route('admin.destroy', $issue->id));

        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas('msg', 'Issue report deleted successfully.');
        
        $this->assertDatabaseMissing('student_issues', ['id' => $issue->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    /** @test */
    public function deleting_non_existent_issue_returns_404()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
                         ->delete(route('admin.destroy', 9999));

        $response->assertStatus(404);
    }
}