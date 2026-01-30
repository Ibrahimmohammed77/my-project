<?php

namespace Tests\Feature\Studio;

use App\Models\Studio;
use App\Models\User;
use App\Models\Photo;
use App\Models\Album;
use App\Models\StorageLibrary;
use App\Models\LookupValue;
use App\Models\LookupMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoReviewControllerTest extends TestCase
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
    }

    /** @test */
    public function it_lists_pending_photos_for_review()
    {
        $library = StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);
        $album = Album::factory()->create(['storage_library_id' => $library->storage_library_id]);
        
        Photo::factory()->count(2)->create([
            'album_id' => $album->album_id,
            'review_status' => Photo::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->get(route('studio.photo-review.pending'));

        $response->assertStatus(200);
        $response->assertViewHas('photos');
    }

    /** @test */
    public function it_lists_pending_photos_as_json()
    {
        $library = StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);
        $album = Album::factory()->create(['storage_library_id' => $library->storage_library_id]);
        
        Photo::factory()->create([
            'album_id' => $album->album_id,
            'review_status' => Photo::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->getJson(route('studio.photo-review.pending'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertCount(1, $response->json('data'));
    }

    /** @test */
    public function it_can_approve_a_photo()
    {
        $library = StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);
        $album = Album::factory()->create(['storage_library_id' => $library->storage_library_id]);
        $photo = Photo::factory()->create([
            'album_id' => $album->album_id,
            'review_status' => Photo::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->postJson(route('studio.photo-review.review', $photo->getKey()), [
                'status' => 'approved',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(Photo::STATUS_APPROVED, $photo->fresh()->review_status);
    }

    /** @test */
    public function it_can_reject_a_photo_with_reason()
    {
        $library = StorageLibrary::factory()->create(['studio_id' => $this->studio->studio_id]);
        $album = Album::factory()->create(['storage_library_id' => $library->storage_library_id]);
        $photo = Photo::factory()->create([
            'album_id' => $album->album_id,
            'review_status' => Photo::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->studioOwner)
            ->postJson(route('studio.photo-review.review', $photo->getKey()), [
                'status' => 'rejected',
                'rejection_reason' => 'Blurry image',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(Photo::STATUS_REJECTED, $photo->fresh()->review_status);
        $this->assertEquals('Blurry image', $photo->fresh()->rejection_reason);
    }
}
