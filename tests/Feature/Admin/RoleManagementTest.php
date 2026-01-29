<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedData();
    }

    private function seedData()
    {
        // Seed Permissions
        $manageRoles = Permission::create([
            'name' => 'manage_roles',
            'display_name' => 'Manage Roles',
            'resource_type' => 'roles',
            'action' => 'manage',
            'is_active' => true
        ]);

        $otherPermission = Permission::create([
            'name' => 'other_permission',
            'display_name' => 'Other Permission',
            'resource_type' => 'other',
            'action' => 'read',
            'is_active' => true
        ]);

        // Seed Admin Role
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Admin Role']);
        $adminRole->permissions()->attach($manageRoles->permission_id);

        // Seed System Role (cannot be deleted)
        Role::create(['name' => 'system_role', 'description' => 'System Role', 'is_system' => true]);

        Gate::define('manage_roles', function (User $user) {
            return $user->roles()->whereHas('permissions', function ($q) {
                $q->where('name', 'manage_roles');
            })->exists();
        });
    }

    public function test_admin_can_access_roles_index()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $response = $this->actingAs($admin)->get(route('spa.roles'));
        
        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    public function test_admin_can_create_role_with_permissions()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $permId = Permission::where('name', 'other_permission')->first()->permission_id;

        $response = $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'New Role',
            'description' => 'New Role Description',
            'permissions' => [$permId],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('roles', ['name' => 'New Role']);
        $role = Role::where('name', 'New Role')->first();
        $this->assertTrue($role->permissions->contains($permId));
    }

    public function test_admin_can_update_role()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $role = Role::create(['name' => 'Role To Update']);
        $permId = Permission::where('name', 'other_permission')->first()->permission_id;

        $response = $this->actingAs($admin)->put(route('admin.roles.update', $role), [
            'name' => 'Updated Role',
            'description' => 'Updated Desc',
            'permissions' => [$permId],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('roles', ['name' => 'Updated Role']);
        $role->refresh();
        $this->assertTrue($role->permissions->contains($permId));
    }

    public function test_admin_can_delete_non_system_role()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $role = Role::create(['name' => 'Delete Me']);

        $response = $this->actingAs($admin)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['name' => 'Delete Me']);
    }

    public function test_admin_cannot_delete_system_role()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $role = Role::where('is_system', true)->first();

        $response = $this->actingAs($admin)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'system_role']);
    }

    public function test_cannot_change_system_role_name()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $role = Role::where('is_system', true)->first();

        $response = $this->actingAs($admin)->put(route('admin.roles.update', $role), [
            'name' => 'Changed Name',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'system_role']);
    }
}
