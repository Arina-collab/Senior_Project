<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function page_renders_with_correct_data()
    {
        User::factory()->count(10)->create(['role' => 'Student']);
        User::factory()->count(2)->create(['role' => 'Admin']);
        Application::factory()->count(5)->create();

        $response = $this->get(route('career.analytics'));

        $response->assertStatus(200);
        
        $response->assertSee('Platform Analytics');
        $response->assertSee('Total Students');
        
        $response->assertViewHas('totalStudents', 10);
        $response->assertViewHas('totalApplications', 5);
    }

    /** @test */
    public function get_chart_data_returns_valid_json_structure()
    {
        $today = Carbon::now()->format('Y-m-d');
        User::factory()->create([
            'role' => 'Student',
            'last_login_at' => Carbon::now()
        ]);

        $response = $this->getJson(route('career.chart_data', ['offset' => 0]));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'labels', 'counts', 'total', 'title'
                 ]);
        $data = $response->json();
        $this->assertEquals(1, $data['total']);
        $this->assertCount(7, $data['labels']);
    }

    /** @test */
    public function chart_data_with_past_week_offset()
    {
        $lastWeekDate = Carbon::now()->subWeek();
        User::factory()->create([
            'role' => 'Student',
            'last_login_at' => $lastWeekDate
        ]);

        $response = $this->getJson(route('career.chart_data', ['offset' => -1]));

        $response->assertStatus(200);
        $data = $response->json();

        $expectedTitle = "Week of " . Carbon::now()->startOfWeek()->subWeek()->format('M d, Y');
        $this->assertEquals($expectedTitle, $data['title']);
    }

    /** @test */
    public function chart_data_handles_zero_students()
    {
        $response = $this->getJson(route('career.chart_data'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(0, $data['total']);
        $this->assertEquals([0, 0, 0, 0, 0, 0, 0], $data['counts']);
    }
    /** @test */
    public function chart_data_ignores_non_integer_offsets()
    {
        $response = $this->getJson(route('career.chart_data', ['offset' => 'abc']));

        $response->assertStatus(200);
        $this->assertEquals(
            "Week of " . Carbon::now()->startOfWeek()->format('M d, Y'), 
            $response->json('title')
        );
    }
}