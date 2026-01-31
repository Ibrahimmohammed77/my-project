<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedData();
    }

    private function seedData()
    {
        // Create lookup masters
        $statusMaster = LookupMaster::create(['code' => 'USER_STATUS', 'name' => 'User Status']);
        $typeMaster = LookupMaster::create(['code' => 'USER_TYPE', 'name' => 'User Type']);

        // Create lookup values
        LookupValue::create([
            'lookup_master_id' => $statusMaster->lookup_master_id,
            'code' => 'ACTIVE',
            'name' => 'Active',
            'is_active' => true
        ]);

        LookupValue::create([
            'lookup_master_id' => $typeMaster->lookup_master_id,
            'code' => 'CUSTOMER',
            'name' => 'Customer',
            'is_active' => true
        ]);

        // Create admin role and permission
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_users',
            'display_name' => 'Manage Users',
            'resource_type' => 'users',
            'action' => 'manage',
            'is_active' => true
        ]);

        $adminRole->permissions()->attach($permission->permission_id);

        // Create customer role
        Role::create(['name' => 'customer', 'display_name' => 'Customer']);

        // Define gate manually for test if auto-registration doesn't catch it in RefreshDatabase context
        Gate::define('manage_users', function (User $user) {
            return $user->hasPermission('manage_users');
        });
    }

    public function test_admin_can_create_user_with_role()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $customerRole = Role::where('name', 'customer')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'newuser',
            'full_name' => 'New User Account',
            'email' => 'newuser@example.com',
            'phone' => '0777777777', // Matches yemeni_phone rule
            'password' => 'Password123!', // Matches strong_password rule
            'role_id' => $customerRole->role_id,
            'user_status_id' => $activeStatus->lookup_value_id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('spa.accounts'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);

        $newUser = User::where('username', 'newuser')->first();
        $this->assertTrue($newUser->hasRole('customer'));
    }

    public function test_non_admin_cannot_create_user()
    {
        $user = User::factory()->create();
        $customerRole = Role::where('name', 'customer')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'username' => 'newuser',
            'full_name' => 'New User Account',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
            'role_id' => $customerRole->role_id,
            'user_status_id' => $activeStatus->lookup_value_id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['username' => 'newuser']);
    }

    public function test_validation_fails_for_invalid_data()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => '', // Empty username
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors(['username', 'email', 'password', 'role_id']);
    }

    public function test_admin_can_list_users_with_pagination()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);
        
        $customerRole = Role::where('name', 'customer')->first();

        // Create users and assign customer role
        $users = User::factory()->count(20)->create();
        foreach ($users as $user) {
            $user->roles()->attach($customerRole->role_id);
        }

        $response = $this->actingAs($admin)->get(route('spa.accounts'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $this->assertCount(15, $response->viewData('users')); // Default per_page is 15
    }

    public function test_admin_can_search_users()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $customerRole = Role::where('name', 'customer')->first();

        $u1 = User::factory()->create(['name' => 'Unique Search Name']);
        $u1->roles()->attach($customerRole->role_id);
        
        $u2 = User::factory()->create(['name' => 'Another User']);
        $u2->roles()->attach($customerRole->role_id);

        $response = $this->actingAs($admin)->get(route('spa.accounts', ['search' => 'Unique']));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $this->assertCount(1, $users);
        $this->assertEquals('Unique Search Name', $users->first()->name);
    }

    public function test_admin_can_filter_users_by_role()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $customerRole = Role::where('name', 'customer')->first();
        $studioRole = Role::create(['name' => 'studio_owner', 'display_name' => 'Studio Owner']);

        $customer = User::factory()->create();
        $customer->roles()->attach($customerRole->role_id);

        $studio = User::factory()->create();
        $studio->roles()->attach($studioRole->role_id);

        $response = $this->actingAs($admin)->get(route('spa.accounts', ['role_id' => $customerRole->role_id]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $this->assertTrue($users->contains($customer));
        $this->assertFalse($users->contains($studio));
    }

    public function test_admin_can_update_user_details_and_role()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $customerRole = Role::where('name', 'customer')->first();
        $studioRole = Role::create(['name' => 'studio_owner', 'display_name' => 'Studio Owner']);
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        // Use safe username
        $user = User::factory()->create(['username' => 'safe_user_1']);
        $user->roles()->attach($customerRole->role_id);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'full_name' => 'Updated Name',
            'username' => 'safe_user_1', // Keeping same username
            'email' => 'updated@example.com',
            'role_id' => $studioRole->role_id,
            'user_status_id' => $activeStatus->lookup_value_id,
            'is_active' => false,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_active' => false,
        ]);

        $user->refresh();
        $this->assertTrue($user->hasRole('studio_owner'));
        $this->assertFalse($user->hasRole('customer'));
    }

    public function test_update_validation_fails_for_duplicate_email()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $user1 = User::factory()->create(['email' => 'user1@example.com', 'username' => 'user1_safe']);
        $user2 = User::factory()->create(['email' => 'user2@example.com', 'username' => 'user2_safe']);
        $role = Role::where('name', 'customer')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user1), [
            'full_name' => 'Updated Name',
            'username' => $user1->username,
            'email' => 'user2@example.com', // Duplicate
            'role_id' => $role->role_id,
            'user_status_id' => $activeStatus->lookup_value_id,
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
