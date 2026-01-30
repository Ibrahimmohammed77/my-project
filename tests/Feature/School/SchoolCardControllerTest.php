<?php

namespace Tests\Feature\School;

use App\Models\Album;
use App\Models\Card;
use App\Models\School;
use App\Models\User;
use App\Models\Role;
use App\Models\LookupValue;
use App\Models\LookupMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolCardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $schoolOwner;
    protected $school;
    protected $cardType;
    protected $cardStatus;
    protected $cardTypeMaster;
    protected $cardStatusMaster;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        Role::firstOrCreate(['name' => 'school-owner']);
        Role::firstOrCreate(['name' => 'studio-owner']);

        // Lookups
        $this->cardTypeMaster = LookupMaster::factory()->create(['code' => 'CARD_TYPE']);
        $this->cardStatusMaster = LookupMaster::factory()->create(['code' => 'CARD_STATUS']);

        $this->cardType = LookupValue::factory()->create([
            'lookup_master_id' => $this->cardTypeMaster->lookup_master_id,
            'code' => 'STANDARD'
        ]);
        $this->cardStatus = LookupValue::factory()->create([
            'lookup_master_id' => $this->cardStatusMaster->lookup_master_id,
            'code' => 'ACTIVE'
        ]);

        // School
        $this->schoolOwner = User::factory()->create();
        $this->schoolOwner->roles()->attach(Role::where('name', 'school-owner')->first()->role_id);
        $this->school = School::factory()->create(['user_id' => $this->schoolOwner->id]);
    }

    /** @test */
    public function it_lists_only_cards_owned_by_the_school()
    {
        // Cards for this school
        Card::factory()->count(3)->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data.cards');
        $response->assertJsonStructure([
            'success',
            'data' => [
                'cards',
                'pagination' => ['total', 'per_page', 'current_page', 'last_page']
            ]
        ]);
    }

    /** @test */
    public function it_can_search_and_filter_school_cards()
    {
        // 1. Target card with specific number and status
        $targetCard = Card::factory()->create([
            'card_number' => 'TARGET123',
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        // 2. Another card for noise (different status)
        $otherStatus = LookupValue::factory()->create([
            'lookup_master_id' => $this->cardStatusMaster->lookup_master_id,
            'code' => 'INACTIVE'
        ]);
        Card::factory()->create([
            'card_number' => 'OTHER456',
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_status_id' => $otherStatus->lookup_value_id,
            'card_type_id' => $this->cardType->lookup_value_id,
        ]);

        // Search test
        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.index', ['search' => 'TARGET']));
        $response->assertJsonCount(1, 'data.cards');

        // Filter status test
        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.index', ['status_id' => $this->cardStatus->lookup_value_id]));
        
        $response->assertJsonCount(1, 'data.cards');
        $this->assertEquals($targetCard->card_id, $response->json('data.cards.0.card_id'));
    }

    /** @test */
    public function it_allows_school_owner_to_view_their_own_card_details()
    {
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.show', $card->card_id));

        $response->assertStatus(200);
        $response->assertJsonPath('data.card.card_id', $card->card_id);
    }

    /** @test */
    public function it_denies_access_to_cards_not_owned_by_the_school()
    {
        $anotherSchool = School::factory()->create();
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.show', $card->card_id));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_link_school_albums_to_school_cards()
    {
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $albums = Album::factory()->count(2)->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $albumIds = $albums->pluck('album_id')->toArray();

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.cards.link-albums', $card->card_id), [
                'album_ids' => $albumIds
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertEquals(2, $card->albums()->count());
    }

    /** @test */
    public function it_prevents_linking_albums_not_owned_by_the_school()
    {
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $anotherSchool = School::factory()->create();
        $foreignAlbum = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.cards.link-albums', $card->card_id), [
                'album_ids' => [$foreignAlbum->album_id]
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('album_ids.0');
        $this->assertEquals(0, $card->albums()->count());
    }

    /** @test */
    public function it_returns_available_albums_in_show_method()
    {
        Album::factory()->count(2)->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.cards.show', $card->card_id));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.availableAlbums');
    }

    /** @test */
    public function it_returns_404_when_linking_albums_to_another_schools_card()
    {
        $anotherSchool = School::factory()->create();
        $otherCard = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.cards.link-albums', $otherCard->card_id), [
                'album_ids' => [$album->album_id]
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_link_multiple_albums_to_a_card()
    {
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'card_type_id' => $this->cardType->lookup_value_id,
            'card_status_id' => $this->cardStatus->lookup_value_id,
        ]);

        $albums = Album::factory()->count(3)->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $albumIds = $albums->pluck('album_id')->toArray();

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.cards.link-albums', $card->card_id), [
                'album_ids' => $albumIds
            ]);

        $response->assertStatus(200);
        $this->assertEquals(3, $card->albums()->count());
    }
}
