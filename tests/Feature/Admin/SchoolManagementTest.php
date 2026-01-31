<?php

namespace Tests\Feature\Admin;

use App\Models\School;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SchoolManagementTest extends TestCase
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
        $schoolTypeMaster = LookupMaster::create(['code' => 'SCHOOL_TYPE', 'name' => 'School Type']);
        $schoolLevelMaster = LookupMaster::create(['code' => 'SCHOOL_LEVEL', 'name' => 'School Level']);

        // Create lookup values
        LookupValue::create([
            'lookup_master_id' => $statusMaster->lookup_master_id,
            'code' => 'ACTIVE',
            'name' => 'Active',
            'is_active' => true
        ]);

        LookupValue::create([
            'lookup_master_id' => $typeMaster->lookup_master_id,
            'code' => 'SCHOOL_OWNER',
            'name' => 'School Owner',
            'is_active' => true
        ]);

        LookupValue::create([
            'lookup_master_id' => $schoolTypeMaster->lookup_master_id,
            'code' => 'PRIVATE',
            'name' => 'Private',
            'is_active' => true
        ]);

        LookupValue::create([
            'lookup_master_id' => $schoolLevelMaster->lookup_master_id,
            'code' => 'SECONDARY',
            'name' => 'Secondary',
            'is_active' => true
        ]);

        // Create admin role and permission
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_schools',
            'display_name' => 'Manage Schools',
            'resource_type' => 'schools',
            'action' => 'manage',
            'is_active' => true
        ]);

        $adminRole->permissions()->attach($permission->permission_id);

        // Create school_owner role
        Role::create(['name' => 'school_owner', 'display_name' => 'School Owner']);

        // Define gate
        Gate::define('manage_schools', function (User $user) {
            return $user->hasPermission('manage_schools');
        });
    }

    public function test_admin_can_list_schools()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $schoolRole = Role::where('name', 'school_owner')->first();
        
        $schools = School::factory()->count(15)->create();
        foreach ($schools as $school) {
            $school->user->roles()->attach($schoolRole->role_id);
        }

        $response = $this->actingAs($admin)->get(route('spa.schools'));

        $response->assertStatus(200);
        $response->assertViewHas('schools');
        $this->assertCount(15, $response->viewData('schools'));
    }

    public function test_admin_can_create_school()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        $privateType = LookupValue::where('code', 'PRIVATE')->first();
        $secondaryLevel = LookupValue::where('code', 'SECONDARY')->first();

        $response = $this->actingAs($admin)->post(route('admin.schools.store'), [
            'name' => 'New School',
            'email' => 'school@example.com',
            'phone' => '0777777777',
            'password' => 'Password123!',
            'school_status_id' => $activeStatus->lookup_value_id,
            'school_type_id' => $privateType->lookup_value_id,
            'school_level_id' => $secondaryLevel->lookup_value_id,
            'city' => 'Sanaa',
            'address' => 'Haddah St',
            'description' => 'Top School',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'تم إنشاء المدرسة بنجاح']);

        $this->assertDatabaseHas('users', [
            'email' => 'school@example.com',
            'name' => 'New School',
        ]);

        $user = User::where('email', 'school@example.com')->first();
        $this->assertTrue($user->hasRole('school_owner'));
        $this->assertNotNull($user->school);
        $this->assertEquals($privateType->lookup_value_id, $user->school->school_type_id);
    }

    public function test_admin_can_update_school()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $schoolRole = Role::where('name', 'school_owner')->first();
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        $privateType = LookupValue::where('code', 'PRIVATE')->first();
        $secondaryLevel = LookupValue::where('code', 'SECONDARY')->first();

        $school = School::factory()->create();
        $school->user->roles()->attach($schoolRole->role_id);

        $response = $this->actingAs($admin)->put(route('admin.schools.update', $school), [
            'name' => 'Updated School Name',
            'email' => 'updated_school@example.com',
            'phone' => '0788888888',
            'username' => 'updated_school_user',
            'school_status_id' => $activeStatus->lookup_value_id,
            'school_type_id' => $privateType->lookup_value_id,
            'school_level_id' => $secondaryLevel->lookup_value_id,
            'city' => 'Aden',
            'address' => 'Main St',
            'description' => 'Updated Description',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $school->user_id,
            'name' => 'Updated School Name',
            'email' => 'updated_school@example.com',
        ]);

        $this->assertDatabaseHas('schools', [
            'school_id' => $school->school_id,
            'city' => 'Aden',
        ]);
    }

    public function test_admin_can_delete_school()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $schoolRole = Role::where('name', 'school_owner')->first();
        $school = School::factory()->create();
        $school->user->roles()->attach($schoolRole->role_id);

        $response = $this->actingAs($admin)->delete(route('admin.schools.destroy', $school));

        $response->assertRedirect(route('spa.schools'));
        
        $this->assertDatabaseMissing('schools', ['school_id' => $school->school_id]);
        $this->assertDatabaseMissing('users', ['id' => $school->user_id]);
    }

    public function test_validation_rules_for_school()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        $response = $this->actingAs($admin)->post(route('admin.schools.store'), [
            'name' => '',
            'email' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'school_status_id', 'school_type_id', 'school_level_id']);
    }
}
