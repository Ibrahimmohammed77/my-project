<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LookupValue;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthRefactoringTest extends TestCase
{
    use RefreshDatabase;

    private function seedData()
    {
        // Create lookup masters
        $statusMaster = LookupMaster::create(['code' => 'user_status', 'name' => 'User Status']);
        $typeMaster = LookupMaster::create(['code' => 'user_type', 'name' => 'User Type']);
        $notifyMaster = LookupMaster::create(['code' => 'notification_type', 'name' => 'Notification Type']);

        // Create lookup values for user type and status
        LookupValue::create([
            'lookup_master_id' => $statusMaster->lookup_master_id,
            'code' => 'active',
            'name' => 'Active',
            'is_active' => true
        ]);
        
        LookupValue::create([
            'lookup_master_id' => $typeMaster->lookup_master_id,
            'code' => 'customer',
            'name' => 'Customer',
            'is_active' => true
        ]);
        
        LookupValue::create([
            'lookup_master_id' => $notifyMaster->lookup_master_id,
            'code' => 'welcome',
            'name' => 'Welcome',
            'is_active' => true
        ]);
        
        // Create role
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_login_page_is_accessible()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_register_page_is_accessible()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'login' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'login' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_view_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewHas('user');
    }
}
