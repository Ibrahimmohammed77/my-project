<?php

namespace Tests\Feature\Admin;

use App\Models\Studio;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class StudioManagementTest extends TestCase
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
            'code' => 'STUDIO_OWNER',
            'name' => 'Studio Owner',
            'is_active' => true
        ]);

        // Create admin role and permission
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_studios',
            'display_name' => 'Manage Studios',
            'resource_type' => 'studios',
            'action' => 'manage',
            'is_active' => true
        ]);

        $adminRole->permissions()->attach($permission->permission_id);

        // Create studio_owner role
        Role::create(['name' => 'studio_owner', 'display_name' => 'Studio Owner']);

        // Define gate
        Gate::define('manage_studios', function (User $user) {
            return $user->hasPermission('manage_studios');
        });
    }

    public function test_admin_can_list_studios()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $studioRole = Role::where('name', 'studio_owner')->first();
        
        $studios = Studio::factory()->count(15)->create();
        foreach ($studios as $studio) {
            $studio->user->roles()->attach($studioRole->role_id);
        }

        $response = $this->actingAs($admin)->get(route('spa.studios'));

        $response->assertStatus(200);
        $response->assertViewHas('studios');
        $this->assertCount(15, $response->viewData('studios'));
    }

    public function test_admin_can_create_studio()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        $response = $this->actingAs($admin)->post(route('admin.studios.store'), [
            'name' => 'New Studio',
            'email' => 'studio@example.com',
            'phone' => '0777777777',
            'password' => 'Password123!',
            'studio_status_id' => $activeStatus->lookup_value_id,
            'city' => 'Sanaa',
            'address' => 'Haddah St',
            'description' => 'Best Studio',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'تم إنشاء الاستوديو بنجاح']);

        $this->assertDatabaseHas('users', [
            'email' => 'studio@example.com',
            'name' => 'New Studio',
        ]);

        $user = User::where('email', 'studio@example.com')->first();
        $this->assertTrue($user->hasRole('studio_owner'));
        $this->assertNotNull($user->studio);
        $this->assertEquals('Sanaa', $user->studio->city);
    }
    public function test_admin_can_update_studio()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $studioRole = Role::where('name', 'studio_owner')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        // Create studio with specific user
        $studio = Studio::factory()->create();
        $studio->user->roles()->attach($studioRole->role_id);

        $response = $this->actingAs($admin)->put(route('admin.studios.update', $studio), [
            'name' => 'Updated Studio Name',
            'email' => 'updated_studio@example.com',
            'phone' => '0788888888',
            'username' => 'updated_studio_user',
            'studio_status_id' => $activeStatus->lookup_value_id,
            'city' => 'Aden',
            'address' => 'Main St',
            'description' => 'Updated Description',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $studio->user_id,
            'name' => 'Updated Studio Name',
            'email' => 'updated_studio@example.com',
        ]);

        $this->assertDatabaseHas('studios', [
            'studio_id' => $studio->studio_id,
            'city' => 'Aden',
            'description' => 'Updated Description',
        ]);
    }

    public function test_admin_can_delete_studio()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $studioRole = Role::where('name', 'studio_owner')->first();
        $studio = Studio::factory()->create();
        $studio->user->roles()->attach($studioRole->role_id);

        $response = $this->actingAs($admin)->delete(route('admin.studios.destroy', $studio));

        $response->assertRedirect(route('spa.studios'));
        
        $this->assertDatabaseMissing('studios', ['studio_id' => $studio->studio_id]);
        $this->assertDatabaseMissing('users', ['id' => $studio->user_id]);
    }

    public function test_validation_rules_for_studio()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $response = $this->actingAs($admin)->post(route('admin.studios.store'), [
            'name' => '',
            'email' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'studio_status_id']);
    }
}
