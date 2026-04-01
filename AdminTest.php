<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Authorizedstaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorize_staff_with_aubg_email()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->post('/admin/authorize-staff', [
                'email' => 'test.user@aubg.edu',
                'role'  => 'editor'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('authorized_staff', [
            'email' => 'test.user@aubg.edu',
            'role'  => 'editor'
        ]);
    }

    /** @test */
    public function fail_non_aubg_domain()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->from('/admin/home') 
            ->post('/admin/authorize-staff', [
                'email' => 'hacker@gmail.com', 
                'role'  => 'admin'
            ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('authorized_staff', 0);
    }

    /** @test */
    public function show_pending_categories()
    {
        DB::table('opportunity_categories')->insert([
            ['name' => 'Approved Cat', 'is_approved' => true],
            ['name' => 'Pending Cat', 'is_approved' => false],
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get('/admin/new-categories');

        $response->assertStatus(200);
        $response->assertViewHas('new');
        
        $categories = $response->viewData('new');
        $this->assertCount(1, $categories);
        $this->assertEquals('Pending Cat', $categories[0]->name);
    }

    /** @test */
    public function approve_category()
    {
        $id = DB::table('opportunity_categories')->insertGetId([
            'name' => 'Verify Me',
            'is_approved' => false
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post("/admin/approve-category/{$id}");

        $this->assertDatabaseHas('opportunity_categories', [
            'id' => $id,
            'is_approved' => 1 
        ]);
        $response->assertSessionHas('success');
    }

    /** @test */
    public function fail_to_update_category_with_empty_name()
    {
        $id = DB::table('opportunity_categories')->insertGetId([
            'name' => 'Old Name',
            'is_approved' => false
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->from('/admin/home')
            ->post("/admin/update-category/{$id}", [
                'name' => ''
            ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('opportunity_categories', ['name' => 'Old Name']);
    }

    /** @test */
    public function delete_category()
    {
        $id = DB::table('opportunity_categories')->insertGetId([
            'name' => 'Delete Me',
            'is_approved' => false
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->delete("/admin/delete-category/{$id}");

        $this->assertDatabaseMissing('opportunity_categories', ['id' => $id]);
    }
}