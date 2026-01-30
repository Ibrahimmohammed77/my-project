<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\Studio;
use App\Models\School;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResponseStandardizationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\LookupSeeder::class);
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        
        // Create Admin User
        $this->adminUser = User::factory()->create([
             'user_type_id' => LookupValue::where('code', 'SUPER_ADMIN')->first()->lookup_value_id,
             'user_status_id' => LookupValue::where('code', 'ACTIVE')->first()->lookup_value_id,
        ]);

        $role = Role::where('name', 'super_admin')->first();
        $this->adminUser->roles()->attach($role);
    }

    public function test_user_index_returns_paginated_response()
    {
        if (!$this->adminUser) $this->markTestSkipped('No admin user found.');

        $response = $this->actingAs($this->adminUser)
                         ->getJson(route('spa.accounts'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'accounts' => []
                     ],
                     'meta' => [
                         'current_page',
                         'last_page', 
                         'per_page',
                         'total'
                     ],
                     'links'
                 ]);
    }

    public function test_plan_index_returns_paginated_response()
    {
        if (!$this->adminUser) $this->markTestSkipped('No admin user found.');

        $response = $this->actingAs($this->adminUser)
                         ->getJson(route('spa.plans'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'plans' => []
                     ],
                     'meta',
                     'links'
                 ]);
    }

    public function test_studio_index_returns_paginated_response()
    {
        if (!$this->adminUser) $this->markTestSkipped('No admin user found.');

        $response = $this->actingAs($this->adminUser)
                         ->getJson(route('spa.studios'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'studios' => []
                     ],
                     'meta',
                     'links'
                 ]);
    }

    public function test_school_index_returns_paginated_response()
    {
        if (!$this->adminUser) $this->markTestSkipped('No admin user found.');

        $response = $this->actingAs($this->adminUser)
                         ->getJson(route('spa.schools'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'schools' => []
                     ],
                     'meta',
                     'links'
                 ]);
    }

    public function test_role_index_returns_success_response()
    {
        if (!$this->adminUser) $this->markTestSkipped('No admin user found.');

        $response = $this->actingAs($this->adminUser)
                         ->getJson(route('spa.roles'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'roles' => []
                     ]
                     // No meta/links for successResponse
                 ]);
    }
}
