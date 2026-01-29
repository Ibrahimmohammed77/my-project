<?php

namespace Tests\Feature\Studio;

use App\Models\Studio;
use App\Models\User;
use App\Models\Card;
use App\Models\LookupValue;
use App\Models\LookupMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $studio;
    protected $studioOwner;

    protected function setUp(): void
    {
        parent::setUp();

        // Create studio owner and studio
        $this->studioOwner = User::factory()->create();
        $this->studio = Studio::factory()->create(['user_id' => $this->studioOwner->id]);
        
        // Link studio back to user
        $this->studioOwner->update(['studio_id' => $this->studio->studio_id]);

        // Setup Card Status and Type
        $statusMaster = LookupMaster::create(['code' => 'CARD_STATUS', 'name' => 'Card Status']);
        $activeStatus = LookupValue::create([
            'lookup_master_id' => $statusMaster->lookup_master_id,
            'code' => 'ACTIVE',
            'name' => 'Active',
            'is_active' => true,
        ]);

        $typeMaster = LookupMaster::create(['code' => 'CARD_TYPE', 'name' => 'Card Type']);
        $cardType = LookupValue::create([
            'lookup_master_id' => $typeMaster->lookup_master_id,
            'code' => 'PHYSICAL',
            'name' => 'Physical',
            'is_active' => true,
        ]);

        // Create a customer who holds a card owned by this studio
        $this->customer = User::factory()->create();
        Card::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'holder_type' => User::class,
            'holder_id' => $this->customer->id,
            'card_status_id' => $activeStatus->lookup_value_id,
            'card_type_id' => $cardType->lookup_value_id,
        ]);
    }

    /** @test */
    public function it_lists_studio_customers()
    {
        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.customers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('spa.studio-customers.index');
    }

    /** @test */
    public function it_lists_studio_customers_as_json()
    {
        $response = $this->actingAs($this->studioOwner)
            ->getJson(route('studio.customers.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'customers'
            ]
        ]);
        
        $this->assertCount(1, $response->json('data.customers'));
    }
}
