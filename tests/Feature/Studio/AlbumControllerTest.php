<?php

namespace Tests\Feature\Studio;

use App\Models\Album;
use App\Models\Plan;
use App\Models\Studio;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlbumControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $studioOwner;
    protected $studio;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a plan
        $this->plan = Plan::factory()->create([
            'max_albums' => 5,
        ]);

        // Create studio owner and studio
        $this->studioOwner = User::factory()->create();
        $this->studio = Studio::factory()->create(['user_id' => $this->studioOwner->id]);
        
        // Link studio back to user
        $this->studioOwner->update(['studio_id' => $this->studio->studio_id]);
        
        // Active subscription
        Subscription::factory()->create([
            'user_id' => $this->studioOwner->id,
            'plan_id' => $this->plan->plan_id,
        ]);
    }

    /** @test */
    public function it_lists_studio_albums()
    {
        Album::factory()->count(3)->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.albums.index'));

        $response->assertStatus(200);
        $response->assertViewHas('albums');
    }

    /** @test */
    public function it_lists_studio_albums_as_json()
    {
        Album::factory()->count(3)->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->getJson(route('studio.albums.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'albums'
            ]
        ]);
    }

    /** @test */
    public function it_can_create_an_album_if_under_plan_limit()
    {
        $this->withoutExceptionHandling();
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);

        $albumData = [
            'name' => 'New Wedding Album',
            'description' => 'A beautiful wedding album',
            'storage_library_id' => $library->storage_library_id,
            'is_visible' => 1,
        ];

        $response = $this->actingAs($this->studioOwner)
            ->from(route('studio.albums.create'))
            ->post(route('studio.albums.store'), $albumData);

        $response->assertRedirect(route('studio.albums.index'));
        $this->assertDatabaseHas('albums', ['name' => 'New Wedding Album']);
    }

    /** @test */
    public function it_can_create_an_album_as_json()
    {
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);

        $albumData = [
            'name' => 'JSON Wedding Album',
            'description' => 'A beautiful wedding album',
            'storage_library_id' => $library->storage_library_id,
            'is_visible' => 1,
        ];

        $response = $this->actingAs($this->studioOwner)
            ->postJson(route('studio.albums.store'), $albumData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم إنشاء الألبوم بنجاح'
        ]);
        $this->assertDatabaseHas('albums', ['name' => 'JSON Wedding Album']);
    }

    /** @test */
    public function it_cannot_create_an_album_if_over_plan_limit()
    {
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);

        // Create albums up to the limit (max_albums = 5)
        Album::factory()->count(5)->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'storage_library_id' => $library->storage_library_id,
        ]);

        $albumData = [
            'name' => 'Over Limit Album',
            'storage_library_id' => $library->storage_library_id,
        ];

        $response = $this->actingAs($this->studioOwner)
            ->from(route('studio.albums.create'))
            ->post(route('studio.albums.store'), $albumData);

        $response->assertRedirect(route('studio.albums.create'));
        $response->assertSessionHas('error', 'تم الوصول للحد الأقصى للألبومات المسموح بها في خطتك');
        $this->assertDatabaseMissing('albums', ['name' => 'Over Limit Album']);
    }

    /** @test */
    public function it_can_update_its_own_album()
    {
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);

        $album = Album::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'storage_library_id' => $library->storage_library_id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->put(route('studio.albums.update', $album->album_id), [
                'name' => 'Updated Name',
                'storage_library_id' => $library->storage_library_id,
            ]);

        $response->assertRedirect(route('studio.albums.index'));
        $this->assertEquals('Updated Name', $album->fresh()->name);
    }

    /** @test */
    public function it_cannot_update_another_studios_album()
    {
        $otherStudio = Studio::factory()->create();
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $otherStudio->studio_id]);

        $album = Album::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $otherStudio->studio_id,
            'storage_library_id' => $library->storage_library_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->put(route('studio.albums.update', $album->album_id), [
                'name' => 'Hacked Name',
                'storage_library_id' => $library->storage_library_id,
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_its_own_album_as_json()
    {
        $library = \App\Models\StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);

        $album = Album::factory()->create([
            'owner_type' => Studio::class,
            'owner_id' => $this->studio->studio_id,
            'storage_library_id' => $library->storage_library_id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->deleteJson(route('studio.albums.destroy', $album->album_id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم حذف الألبوم بنجاح'
        ]);
        $this->assertSoftDeleted('albums', ['album_id' => $album->album_id]);
    }
}
