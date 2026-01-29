<?php

namespace Tests\Feature\Admin;

use App\Models\LookupMaster;
use App\Models\LookupValue;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class LookupManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedData();
    }

    private function seedData()
    {
        // Seed Roles & Permissions
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_lookups',
            'display_name' => 'Manage Lookups',
            'resource_type' => 'lookups',
            'action' => 'manage',
            'is_active' => true
        ]);
        $adminRole->permissions()->attach($permission->permission_id);

        Gate::define('manage_lookups', function (User $user) {
            return $user->hasPermission('manage_lookups');
        });
    }

    public function test_admin_can_access_lookups_index()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        LookupMaster::create(['code' => 'test_master', 'name' => 'Test Master']);

        $response = $this->actingAs($admin)->get(route('spa.lookups'));
        
        $response->assertStatus(200);
        $response->assertViewHas('masters');
    }

    public function test_non_admin_cannot_access_lookups()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('spa.lookups'));
        $response->assertStatus(403);
    }

    public function test_admin_can_add_lookup_value()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $master = LookupMaster::create(['code' => 'colors', 'name' => 'Colors']);

        $response = $this->actingAs($admin)->post(route('admin.lookups.values.store'), [
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'red',
            'name' => 'Red',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lookup_values', ['code' => 'red', 'lookup_master_id' => $master->lookup_master_id]);
    }

    public function test_admin_can_update_lookup_value()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $master = LookupMaster::create(['code' => 'sizes', 'name' => 'Sizes']);
        $value = LookupValue::create([
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 's',
            'name' => 'Small',
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->put(route('admin.lookups.values.update', $value), [
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'sm',
            'name' => 'Small Modified',
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lookup_values', ['code' => 'sm', 'is_active' => false]);
    }

    public function test_admin_can_delete_lookup_value()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $master = LookupMaster::create(['code' => 'types', 'name' => 'Types']);
        $value = LookupValue::create([
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'type1',
            'name' => 'Type 1',
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.lookups.values.destroy', $value));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lookup_values', ['code' => 'type1']);
    }

    public function test_validation_fails_for_duplicate_code_in_same_master()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $master = LookupMaster::create(['code' => 'countries', 'name' => 'Countries']);
        LookupValue::create([
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'ye',
            'name' => 'Yemen',
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->post(route('admin.lookups.values.store'), [
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'ye', // Duplicate
            'name' => 'Yemen Duplicate',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors(['code']);
    }
}
