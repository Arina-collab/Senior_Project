<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Opportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerOpportunitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function update_opp_with_valid_data()
    {
        $opportunity = Opportunity::factory()->create();
        
        $newData = [
            'title'        => 'Updated Title',
            'company_name' => 'New Corp',
            'category'     => 'IT',
            'type'         => 'Full-time',
            'description'  => 'Updated description content.',
            'location'     => 'New York',
            'deadline'     => '2026-12-31',
            'hr_email'     => 'hr@newcorp.com',
            'is_priority'  => true,
        ];

        $response = $this->put(route('career.opportunities.update', $opportunity), $newData);

        $response->assertRedirect(route('career.dashboard'));
        $response->assertSessionHas('success_app');
        
        $this->assertDatabaseHas('opportunities', [
            'id'    => $opportunity->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function error_bad_input()
    {
        $opportunity = Opportunity::factory()->create();

        // Empty/invalid data
        $badData = [
            'title'            => '', 
            'hr_email'         => 'not-an-email', 
            'application_link' => 'random-string',
        ];

        $response = $this->from(route('career.opportunities.edit', $opportunity))
                         ->put(route('career.opportunities.update', $opportunity), $badData);

        $response->assertStatus(302);
        $response->assertRedirect(route('career.opportunities.edit', $opportunity));

        $response->assertSessionHasErrors(['title', 'hr_email', 'application_link']);
        
        $errors = session('errors')->getBag('default');
        $this->assertEquals('The title field is required.', $errors->first('title'));
        $this->assertEquals('The hr email must be a valid email address.', $errors->first('hr_email'));
    }

    /** @test */
    public function error_if_edit_non_existent_opp()
    {
        $response = $this->get('/career/opportunities/999/edit');
        $response->assertStatus(404);
    }

    /** @test */
    public function delete_opp()
    {
        $opportunity = Opportunity::factory()->create();

        $response = $this->delete(route('career.opportunities.destroy', $opportunity));

        $response->assertRedirect(route('career.dashboard'));
        $this->assertDatabaseMissing('opportunities', ['id' => $opportunity->id]);
    }
}