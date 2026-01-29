<?php

namespace Tests\Feature\Studio;

use App\Models\Album;
use App\Models\Card;
use App\Models\Plan;
use App\Models\Studio;
use App\Models\Subscription;
use App\Models\User;
use App\Models\LookupValue;
use App\Models\StorageLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CardControllerTest extends TestCase
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

        // Fix Gate for testing
        Gate::define('is-studio-owner', function ($user) {
            return true;
        });

        // Add dummy status and types for cards
        $master = \App\Models\LookupMaster::create([
            'code' => 'CARD_STATUS',
            'name' => 'Card Status',
        ]);

        $this->activeStatus = LookupValue::create([
            'lookup_master_id' => $master->lookup_master_id,
            'code' => 'ACTIVE',
            'name' => 'Active',
            'is_active' => true,
        ]);

        $typeMaster = \App\Models\LookupMaster::create([
            'code' => 'CARD_TYPE',
            'name' => 'Card Type',
        ]);

        $this->cardType = LookupValue::create([
            'lookup_master_id' => $typeMaster->lookup_master_id,
            'code' => 'PHYSICAL',
            'name' => 'Physical Card',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_lists_studio_cards()
    {
        Card::factory()->count(3)->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'card_status_id' => $this->activeStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.cards.index'));

        $response->assertStatus(200);
        $response->assertViewHas('cards');
    }

    /** @test */
    public function it_can_view_a_card()
    {
        $card = Card::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'card_status_id' => $this->activeStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.cards.show', $card->card_id));

        $response->assertStatus(200);
        $response->assertViewHas('card');
    }

    /** @test */
    public function it_can_link_albums_to_a_card()
    {
        $card = Card::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'card_status_id' => $this->activeStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        $library = StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);
        $albums = Album::factory()->count(2)->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'storage_library_id' => $library->storage_library_id,
        ]);

        $albumIds = $albums->pluck('album_id')->toArray();

        $response = $this->actingAs($this->studioOwner)
            ->post(route('studio.cards.link-albums', $card->card_id), [
                'album_ids' => $albumIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(2, $card->fresh()->albums()->count());
    }

    /** @test */
    public function it_cannot_link_other_studios_albums()
    {
        $card = Card::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'card_status_id' => $this->activeStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        $otherStudio = Studio::factory()->create();
        $otherLibrary = StorageLibrary::factory()->create(['studio_id' => $otherStudio->studio_id]);
        $otherAlbum = Album::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $otherStudio->studio_id,
            'storage_library_id' => $otherLibrary->storage_library_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->post(route('studio.cards.link-albums', $card->card_id), [
                'album_ids' => [$otherAlbum->album_id],
            ]);

        $response->assertSessionHasErrors(['album_ids.0']);
        $this->assertEquals(0, $card->fresh()->albums()->count());
    }
}
