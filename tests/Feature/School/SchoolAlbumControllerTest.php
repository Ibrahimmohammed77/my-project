<?php

namespace Tests\Feature\School;

use App\Models\Album;
use App\Models\Card;
use App\Models\Plan;
use App\Models\School;
use App\Models\StorageLibrary;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolAlbumControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $schoolOwner;
    protected $school;
    protected $plan;
    protected $storageLibrary;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'school-owner']);
        Role::firstOrCreate(['name' => 'studio-owner']);

        // Create a plan for schools
        $this->plan = Plan::factory()->create([
            'max_albums' => 5,
        ]);

        // Create school owner and school
        $this->schoolOwner = User::factory()->create();
        $this->schoolOwner->roles()->attach(Role::where('name', 'school-owner')->first()->role_id);
        
        $this->school = School::factory()->create(['user_id' => $this->schoolOwner->id]);
        
        // Active subscription
        Subscription::factory()->create([
            'user_id' => $this->schoolOwner->id,
            'plan_id' => $this->plan->plan_id,
        ]);

        // Create a storage library for this school
        $this->storageLibrary = StorageLibrary::factory()->create([
            'school_id' => $this->school->school_id,
            'user_id' => $this->schoolOwner->id,
            'name' => 'School Storage'
        ]);
    }

    /** @test */
    public function it_lists_school_albums()
    {
        Album::factory()->count(3)->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.albums.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data.albums');
        $response->assertJsonStructure([
            'success',
            'data' => [
                'albums',
                'pagination' => ['total', 'per_page', 'current_page', 'last_page']
            ]
        ]);
    }

    /** @test */
    public function it_can_search_school_albums()
    {
        Album::factory()->create([
            'name' => 'Search Target',
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.albums.index', ['search' => 'Target']));

        $response->assertJsonCount(1, 'data.albums');
        $this->assertEquals('Search Target', $response->json('data.albums.0.name'));
    }

    /** @test */
    public function it_can_create_an_album_with_automatic_storage_resolution()
    {
        $albumData = [
            'name' => 'Graduation 2026',
            'description' => 'The big day',
            'is_visible' => 1,
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.albums.store'), $albumData);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('albums', [
            'name' => 'Graduation 2026',
            'storage_library_id' => $this->storageLibrary->storage_library_id,
            'owner_type' => 'App\Models\School',
            'owner_id' => $this->school->school_id
        ]);
    }

    /** @test */
    public function it_denies_access_to_studio_owners()
    {
        $studioOwner = User::factory()->create();
        $studioOwner->roles()->attach(Role::where('name', 'studio-owner')->first()->role_id);

        $response = $this->actingAs($studioOwner)
            ->getJson(route('school.albums.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_cannot_create_album_if_no_storage_library_assigned()
    {
        // Delete the storage library
        $this->storageLibrary->delete();

        $albumData = [
            'name' => 'Faulty Album',
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.albums.store'), $albumData);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'لم يتم العثور على مكتبة تخزين مخصصة للمدرسة. يرجى التواصل مع الإدارة.');
    }

    /** @test */
    public function it_can_create_an_album_linked_to_cards()
    {
        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $albumData = [
            'name' => 'Album with Cards',
            'card_ids' => [$card->card_id]
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->postJson(route('school.albums.store'), $albumData);

        $response->assertStatus(200);
        $this->assertEquals(1, $card->albums()->count());
    }

    /** @test */
    public function it_can_update_an_album()
    {
        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
            'name' => 'Old Name'
        ]);

        $updateData = [
            'name' => 'New Name',
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.albums.update', $album->album_id), $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('albums', [
            'album_id' => $album->album_id,
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function it_can_update_an_album_and_relink_cards()
    {
        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $card = Card::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $updateData = [
            'name' => 'Updated Album',
            'card_ids' => [$card->card_id]
        ];

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.albums.update', $album->album_id), $updateData);

        $response->assertStatus(200);
        $this->assertEquals(1, $card->albums()->count());
    }

    /** @test */
    public function it_can_delete_an_album()
    {
        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->deleteJson(route('school.albums.destroy', $album->album_id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('albums', ['album_id' => $album->album_id]);
    }

    /** @test */
    public function it_prevents_updating_other_schools_album()
    {
        $anotherSchool = School::factory()->create();
        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->putJson(route('school.albums.update', $album->album_id), ['name' => 'Hack']);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_prevents_deleting_other_schools_album()
    {
        $anotherSchool = School::factory()->create();
        $album = Album::factory()->create([
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->deleteJson(route('school.albums.destroy', $album->album_id));

        $response->assertStatus(404);
    }
}
