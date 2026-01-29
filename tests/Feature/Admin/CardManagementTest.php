<?php

namespace Tests\Feature\Admin;

use App\Models\CardGroup;
use App\Models\Card;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CardManagementTest extends TestCase
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
        $cardTypeMaster = LookupMaster::create(['code' => 'card_type', 'name' => 'Card Type']);
        LookupValue::create(['lookup_master_id' => $cardTypeMaster->lookup_master_id, 'code' => 'physical', 'name' => 'Physical', 'is_active' => true]);
        
        $cardStatusMaster = LookupMaster::create(['code' => 'card_status', 'name' => 'Card Status']);
        LookupValue::create(['lookup_master_id' => $cardStatusMaster->lookup_master_id, 'code' => 'active', 'name' => 'Active', 'is_active' => true]);


        // Seed Roles & Permissions
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $permission = Permission::create([
            'name' => 'manage_cards',
            'display_name' => 'Manage Cards',
            'resource_type' => 'cards',
            'action' => 'manage',
            'is_active' => true
        ]);
        $adminRole->permissions()->attach($permission->permission_id);

        Gate::define('manage_cards', function (User $user) {
            return $user->hasPermission('manage_cards');
        });
    }

    // --- Group Tests ---

    public function test_admin_can_access_card_groups_index()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        CardGroup::create(['name' => 'Test Group', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->get(route('spa.cards'));
        
        $response->assertStatus(200);
        $response->assertViewHas('groups');
    }

    public function test_admin_can_create_card_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $response = $this->actingAs($admin)->post(route('admin.cards.groups.store'), [
            'name' => 'New Group',
            'description' => 'Test Desc',
            'sub_card_available' => 500,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('card_groups', ['name' => 'New Group', 'sub_card_available' => 500]);
    }

    public function test_admin_can_update_card_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $group = CardGroup::create(['name' => 'Old Group', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->put(route('admin.cards.groups.update', $group), [
            'name' => 'Updated Group',
            'description' => 'Updated Desc',
            'sub_card_available' => 200,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('card_groups', ['name' => 'Updated Group', 'sub_card_available' => 200]);
    }

    public function test_admin_can_delete_card_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $group = CardGroup::create(['name' => 'To Delete', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->delete(route('admin.cards.groups.destroy', $group));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('card_groups', ['name' => 'To Delete']);
    }

    // --- Nested Card Tests ---

    public function test_admin_can_access_cards_of_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $group = CardGroup::create(['name' => 'Group A', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->get(route('admin.cards.groups.cards', $group));

        $response->assertStatus(200);
        $response->assertViewHas(['group', 'cards']);
    }

    public function test_admin_can_create_card_in_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
        
        $group = CardGroup::create(['name' => 'Group B', 'sub_card_available' => 100]);
        $type = LookupValue::where('code', 'physical')->first();
        $status = LookupValue::where('code', 'active')->first();

        $response = $this->actingAs($admin)->post(route('admin.cards.groups.cards.store', $group), [
            'card_group_id' => $group->group_id,
            'card_type_id' => $type->lookup_value_id,
            'card_status_id' => $status->lookup_value_id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cards', ['card_group_id' => $group->group_id]);
    }

    public function test_card_creation_auto_generates_uuid()
    {
         $admin = User::factory()->create();
         $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id);
         
         $group = CardGroup::create(['name' => 'Group C', 'sub_card_available' => 100]);
         $type = LookupValue::where('code', 'physical')->first();
         $status = LookupValue::where('code', 'active')->first();
 
         $this->actingAs($admin)->post(route('admin.cards.groups.cards.store', $group), [
             'card_group_id' => $group->group_id,
             'card_type_id' => $type->lookup_value_id,
             'card_status_id' => $status->lookup_value_id,
         ]);

         $card = Card::where('card_group_id', $group->group_id)->first();
         $this->assertNotNull($card->card_uuid);
    }
}
