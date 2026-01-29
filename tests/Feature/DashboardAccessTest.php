<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        $this->seed([\Database\Seeders\LookupSeeder::class, \Database\Seeders\SystemSeeder::class]);

        // Re-register dynamic gates for the seeded permissions
        $permissions = \App\Models\Permission::active()->get();
        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission->name);
            });
        }
    }

    public function test_admin_is_redirected_to_admin_dashboard()
    {
        $adminType = LookupValue::where('code', 'SUPER_ADMIN')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        
        $admin = User::factory()->create([
            'user_type_id' => $adminType->lookup_value_id,
            'user_status_id' => $activeStatus->lookup_value_id,
            'is_active' => true,
            'phone' => '+966500000001', // Required for profile completion
        ]);
        
        $role = Role::where('name', 'admin')->first();
        $admin->roles()->attach($role->role_id, ['is_active' => true]);
        
        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('dashboard.admin'));
        
        $response = $this->actingAs($admin)->get(route('dashboard.admin'));
        $response->assertStatus(200);
    }

    public function test_studio_owner_is_redirected_to_studio_dashboard()
    {
        $studioType = LookupValue::where('code', 'STUDIO_OWNER')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        
        $user = User::factory()->create([
            'user_type_id' => $studioType->lookup_value_id,
            'user_status_id' => $activeStatus->lookup_value_id,
            'is_active' => true,
            'phone' => '+966500000002', // Required for profile completion
        ]);
        
        $role = Role::where('name', 'studio_owner')->first();
        $user->roles()->attach($role->role_id, ['is_active' => true]);

        // Mock studio creation to avoid profile completion redirect
        $user->studio()->create([
            'name_ar' => 'Test Studio',
            'name_en' => 'Test Studio',
            'slug' => 'test-studio',
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('dashboard.studio-owner'));
        
        $response = $this->actingAs($user)->get(route('dashboard.studio-owner'));
        $response->assertStatus(200);
    }

    public function test_user_without_dashboard_permissions_is_redirected_to_guest_dashboard()
    {
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        $guestType = LookupValue::where('code', 'CUSTOMER')->first(); // Just for passing check

        $user = User::factory()->create([
            'is_active' => true,
            'user_type_id' => $guestType->lookup_value_id,
            'user_status_id' => $activeStatus->lookup_value_id,
            'phone' => '+966500000003', // Required for profile completion
        ]);
        // No roles attached

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('dashboard.guest'));
    }
}
