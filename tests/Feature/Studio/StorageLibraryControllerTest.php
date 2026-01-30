<?php

namespace Tests\Feature\Studio;

use App\Models\Studio;
use App\Models\User;
use App\Models\StorageLibrary;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageLibraryControllerTest extends TestCase
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

        // Active subscription
        $plan = Plan::factory()->create(['max_storage_libraries' => 10]);
        Subscription::factory()->create([
            'user_id' => $this->studioOwner->id,
            'plan_id' => $plan->plan_id,
        ]);
    }

    /** @test */
    public function it_lists_storage_libraries()
    {
        StorageLibrary::factory()->count(2)->create([
            'studio_id' => $this->studio->studio_id,
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.storage.index'));

        $response->assertStatus(200);
        $response->assertViewHas('libraries');
    }

    /** @test */
    public function it_can_create_storage_library_as_json()
    {
        $subscriber = User::factory()->create();
        $data = [
            'subscriber_id' => $subscriber->id,
            'name' => 'Test Storage',
            'description' => 'Test Description',
            'storage_limit' => 1, // 1MB
        ];

        $response = $this->actingAs($this->studioOwner)
            ->postJson(route('studio.storage.store'), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('storage_libraries', [
            'name' => 'Test Storage',
            'storage_limit' => 1048576, // Should be stored as 1MB in bytes
        ]);
    }

    /** @test */
    public function it_can_update_storage_library_as_json()
    {
        $library = StorageLibrary::factory()->create([
            'studio_id' => $this->studio->studio_id,
            'user_id' => User::factory()->create()->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->putJson(route('studio.storage.update', $library->storage_library_id), [
                'name' => 'New Name',
                'storage_limit' => 2, // 2MB
            ]);

        $response->assertStatus(200);
        $this->assertEquals('New Name', $library->fresh()->name);
        $this->assertEquals(2097152, $library->fresh()->storage_limit); // Should be stored as 2MB in bytes
    }

    /** @test */
    public function it_can_delete_empty_storage_library()
    {
        $library = StorageLibrary::factory()->create([
            'studio_id' => $this->studio->studio_id,
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->deleteJson(route('studio.storage.destroy', $library->storage_library_id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('storage_libraries', ['storage_library_id' => $library->storage_library_id]);
    }

    /** @test */
    public function it_cannot_delete_library_with_content()
    {
        $library = StorageLibrary::factory()->create([
            'studio_id' => $this->studio->studio_id,
            'user_id' => User::factory()->create()->id,
        ]);

        $album = Album::factory()->create(['storage_library_id' => $library->storage_library_id]);
        Photo::factory()->create([
            'album_id' => $album->album_id,
            'file_size' => 1000,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->deleteJson(route('studio.storage.destroy', $library->storage_library_id));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('storage_libraries', ['storage_library_id' => $library->storage_library_id]);
    }
}
