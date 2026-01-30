<?php

namespace Tests\Feature\Admin;

use App\Models\Plan;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PlanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedData();
    }

    private function seedData()
    {
        // Seed Lookups
        $billingCycleMaster = LookupMaster::create(['code' => 'billing_cycle', 'name' => 'Billing Cycle']);
        LookupValue::create(['lookup_master_id' => $billingCycleMaster->lookup_master_id, 'code' => 'monthly', 'name' => 'Monthly', 'is_active' => true]);
        LookupValue::create(['lookup_master_id' => $billingCycleMaster->lookup_master_id, 'code' => 'yearly', 'name' => 'Yearly', 'is_active' => true]);

        // Seed Roles & Permissions
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_plans',
            'display_name' => 'Manage Plans',
            'resource_type' => 'plans',
            'action' => 'manage',
            'is_active' => true
        ]);
        $adminRole->permissions()->attach($permission->permission_id);

        Gate::define('manage_plans', function (User $user) {
            return $user->hasPermission('manage_plans');
        });
    }

    public function test_admin_can_create_plan()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $response = $this->actingAs($admin)->post(route('admin.plans.store'), [
            'name' => 'Pro Plan',
            'description' => 'Best for pros',
            'storage_limit' => 50,
            'price_monthly' => 2000,
            'price_yearly' => 20000,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('plans', ['name' => 'Pro Plan']);
    }

    public function test_non_admin_cannot_access_plans()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('spa.plans'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_plan()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $plan = Plan::create([
            'name' => 'Basic Plan',
            'description' => 'Basic',
            'storage_limit' => 10,
            'price_monthly' => 1000,
            'price_yearly' => 10000,
            'features' => ['f1'],
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->put(route('admin.plans.update', $plan), [
            'name' => 'Updated Plan',
            'description' => 'Updated Desc',
            'storage_limit' => 20,
            'price_monthly' => 1500,
            'price_yearly' => 15000,
            'features' => ['f1', 'f2'],
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('plans', ['name' => 'Updated Plan', 'is_active' => false]);
    }

    public function test_admin_can_delete_plan()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $plan = Plan::create([
            'name' => 'To Delete',
            'description' => 'Desc',
            'storage_limit' => 1,
            'price_monthly' => 100,
            'price_yearly' => 1000,
            'features' => ['f1'],
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('plans', ['name' => 'To Delete']);
    }
}
