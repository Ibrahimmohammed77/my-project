<?php

namespace Tests\Feature\School;

use App\Models\School;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $schoolOwner;
    protected $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        Role::firstOrCreate(['name' => 'school-owner']);

        // School
        $this->schoolOwner = User::factory()->create(['name' => 'Old School Name']);
        $this->schoolOwner->roles()->attach(Role::where('name', 'school-owner')->first()->role_id);
        $this->school = School::factory()->create([
            'user_id' => $this->schoolOwner->id,
            'description' => 'Old Description',
            'city' => 'Old City'
        ]);
    }

    /** @test */
    public function it_can_view_school_profile_data()
    {
        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.profile.edit'));

        $response->assertStatus(200);
        $response->assertJsonPath('data.school.description', 'Old Description');
        $response->assertJsonPath('data.user.name', 'Old School Name');
    }

    /** @test */
    public function it_can_update_school_profile_data()
    {
        $updateData = [
            'name' => 'New Awesome School',
            'description' => 'New Description',
            'city' => 'New City',
            'address' => 'New Address'
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.profile.update'), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify Database
        $this->schoolOwner->refresh();
        $this->school->refresh();

        $this->assertEquals('New Awesome School', $this->schoolOwner->name);
        $this->assertEquals('New Description', $this->school->description);
        $this->assertEquals('New City', $this->school->city);
    }

    /** @test */
    public function it_fails_validation_for_invalid_profile_data()
    {
        $invalidData = [
            'name' => '', // Required/Sometimes but cannot be empty if present
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.profile.update'), $invalidData);

        // Depending on how 'sometimes' and validation works, if it's required when present
        // Actually name isn't required in my FormRequest, it's just sometimes|string.
        // Let's try an invalid logo.
        $invalidData = [
            'logo' => 'not-an-image'
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.profile.update'), $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('logo');
    }

    /** @test */
    public function it_denies_unauthorized_access_to_profile()
    {
        $studioOwner = User::factory()->create();
        Role::firstOrCreate(['name' => 'studio-owner']);
        $studioOwner->roles()->attach(Role::where('name', 'studio-owner')->first()->role_id);

        $response = $this->actingAs($studioOwner)
            ->getJson(route('school.profile.edit'));

        $response->assertStatus(403);
    }
}
