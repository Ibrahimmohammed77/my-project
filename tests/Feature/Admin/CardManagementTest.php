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
        $this->seed([\Database\Seeders\LookupSeeder::class, \Database\Seeders\SystemSeeder::class]);
        
        Gate::define('manage_cards', function (User $user) {
            return $user->hasPermission('manage_cards');
        });
    }

    // --- Group Tests ---

    public function test_admin_can_access_card_groups_index()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
        CardGroup::create(['name' => 'Test Group', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->get(route('spa.cards'));
        
        $response->assertStatus(200);
        $response->assertViewHas('groups');
    }

    public function test_admin_can_create_card_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
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
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
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
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
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
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
        $group = CardGroup::create(['name' => 'Group A', 'sub_card_available' => 100]);

        $response = $this->actingAs($admin)->get(route('admin.cards.groups.cards', $group));

        $response->assertStatus(200);
        $response->assertViewHas(['group', 'cards']);
    }

    public function test_admin_can_create_card_in_group()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
        
        $group = CardGroup::create(['name' => 'Group B', 'sub_card_available' => 100]);
        $type = LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))
            ->where('code', 'STANDARD')
            ->first();
        $status = LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))
            ->where('code', 'ACTIVE')
            ->first();

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
         $admin->roles()->attach(Role::where('name', 'admin')->first()->role_id, ['is_active' => true]);
         
         $group = CardGroup::create(['name' => 'Group C', 'sub_card_available' => 100]);
         $type = LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))
            ->where('code', 'STANDARD')
            ->first();
         $status = LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))
            ->where('code', 'ACTIVE')
            ->first();
 
         $response = $this->actingAs($admin)->post(route('admin.cards.groups.cards.store', $group), [
            'card_group_id' => $group->group_id,
            'card_type_id' => $type->lookup_value_id,
            'card_status_id' => $status->lookup_value_id,
        ]);
         
         $card = Card::where('card_group_id', $group->group_id)->first();
         $this->assertNotNull($card->card_uuid);
    }
}
