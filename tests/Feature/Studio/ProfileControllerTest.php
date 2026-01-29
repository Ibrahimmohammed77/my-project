<?php

namespace Tests\Feature\Studio;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $studio;
    protected $studioOwner;

    protected function setUp(): void
    {
        parent::setUp();

        // Create studio owner and studio
        $this->studioOwner = User::factory()->create();
        $this->studio = Studio::factory()->create(['user_id' => $this->studioOwner->id]);
        
        // Link studio back to user
        $this->studioOwner->update(['studio_id' => $this->studio->studio_id]);
    }

    /** @test */
    public function it_can_view_profile_edit_page()
    {
        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('spa.studio-profile.index');
    }

    /** @test */
    public function it_can_update_profile_as_json()
    {
        $response = $this->actingAs($this->studioOwner)
            ->putJson(route('studio.profile.update'), [
                'name' => 'Updated Studio Name',
                'phone' => '123456789',
                'website' => 'https://example.com',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('Updated Studio Name', $this->studioOwner->fresh()->name);
    }
}
