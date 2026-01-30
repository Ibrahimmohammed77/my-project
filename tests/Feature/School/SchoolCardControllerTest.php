<?php

namespace Tests\Feature\School;

use App\Models\Album;
use App\Models\Card;
use App\Models\School;
use App\Models\User;
use App\Models\Role;
use App\Models\LookupValue;
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

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        Role::firstOrCreate(['name' => 'school-owner']);
        Role::firstOrCreate(['name' => 'studio-owner']);

        // Lookups
        $this->cardType = LookupValue::factory()->create(['code' => 'STANDARD']);
        $this->cardStatus = LookupValue::factory()->create(['code' => 'ACTIVE']);

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
}
